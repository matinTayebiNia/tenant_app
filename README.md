# Multi-Tenant Inventory Management System

A Laravel + MySQL backend for managing stock across multiple tenants (companies), with
concurrency-safe stock movements and real-time reconciliation support.

## Setup

The project is fully Dockerized. A `Makefile` wraps the common Docker Compose commands.

```bash
# One-command bootstrap: copies .env.example -> .env if missing, builds images,
# brings up the full stack (app, queue, scheduler, database).
make setup

# Day-to-day usage once the stack exists:
make up          # start containers
make down         # stop containers
make restart       # restart containers
make logs         # tail logs from all services
make ps           # list running containers

# Database
make migrate        # run migrations
make seed          # seed demo data

# Shell access
make shell         # open a bash shell inside the app container
make artisan <cmd>    # run any artisan command, e.g. `make artisan inventory:reconcile 1`
```

`make setup` already runs migrations and seeds demo data as part of bringing the stack up
(the `migrate` service runs to completion before `app`, `queue`, and `scheduler` start), so
a fresh clone only needs `make setup` to be ready to use.

The application server runs on **Laravel Octane with FrankenPHP**, not the standard
`php artisan serve` / PHP-FPM setup — the `app` service starts via
`php artisan octane:start --server=frankenphp`. This keeps the application booted in
memory between requests for lower latency, which matters for the sub-200ms performance
target on `GET /api/v1/stock-levels`.

Environment variables are configured via `.env` (copied automatically from `.env.example`
by `make setup` if it doesn't already exist). No additional service-specific configuration
is required beyond standard Laravel DB credentials.

## Architectural Decisions

### Multi-Tenancy

Tenant isolation is implemented at the **row level** using a `tenant_id` column on every
tenant-scoped table (`products`, `warehouses`, `stock_movements`, `stock_levels`), rather
than separate databases per tenant. This keeps operational overhead low (single schema,
single connection pool) while still guaranteeing isolation through a **global Eloquent
scope** applied to all tenant-scoped models.

The tenant is resolved once per request, inside a dedicated middleware:

1. The middleware inspects the incoming request (subdomain or `X-Tenant-ID` header) and
   resolves the corresponding `Tenant` record.
2. The resolved tenant is stored in a central runtime context (`TenantScopeConfig`), which
   acts as the single source of truth for "who is the current tenant" for the remainder of
   the request lifecycle.
3. The global scope reads from this context to automatically filter every query — models
   never need to manually specify `tenant_id` in application code.

This keeps tenant-filtering logic out of controllers and services entirely: as long as a
model uses the scope, cross-tenant data leakage at the query level is structurally
prevented rather than relying on developers remembering to filter manually.

### Concurrency & Stock Correctness

Two complementary strategies are used, depending on the situation:

**1. Pessimistic locking (`lockForUpdate`) for existing rows.**
Every stock mutation (`in`, `out`, `transfer`) runs inside a `DB::transaction()` and
acquires a row-level lock (`SELECT ... FOR UPDATE`) on the relevant `stock_levels` row(s)
before reading or modifying quantity. This guarantees that concurrent requests touching the
same product/warehouse combination are serialized at the database level, preventing lost
updates and negative stock.

**2. Optimistic insert-then-retry for first-time stock records.**
When a `stock_levels` row does not yet exist for a given `product_id` + `warehouse_id` pair,
there is nothing to lock. Two concurrent requests may both attempt to `INSERT` the initial
row; the unique constraint on `(tenant_id, product_id, warehouse_id)` guarantees only one
succeeds. The loser catches the resulting `QueryException` (MySQL error code `1062`) and
retries the operation, which now finds the row created by the winner and proceeds with a
locked `increment`/`decrement` as usual.

**3. Deadlock avoidance via consistent lock ordering (transfers).**
A naive implementation of `transfer` locks the source warehouse row first, then the
destination row. Under concurrent, opposite-direction transfers (A→B and B→A happening at
the same time), this ordering can deadlock: each transaction holds the lock the other one
is waiting for. To prevent this, lock acquisition order is normalized by `warehouse_id`
(the numerically smaller ID is always locked first), independent of which one is logically
the source or destination for a given request. This makes deadlock structurally impossible
for this operation, rather than relying on MySQL's deadlock detector and retry logic.

**Concurrency test.** The required test (100 concurrent `out` movements of quantity 1
against a stock of 50) is simulated using Laravel's queue system with multiple concurrent
workers, each processing one movement request independently, to produce genuine
database-level concurrency rather than sequential requests in a single process. Jobs are
dispatched to a dedicated `stock-concurrency-test` Redis queue, consumed by a separate
`stock-test-worker` Docker Compose service (distinct from the application's regular `queue`
worker) so that concurrency testing never competes with, or is throttled by, normal
application queue processing. The test asserts exactly 50 succeeds, 50 domain-exception
failures, and a final quantity of 0.

### Indexing Strategy

| Index | Purpose                                                                                                                                                                     |
|---|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `UNIQUE (tenant_id, product_id, warehouse_id)` on `stock_levels` | Enforces one stock-level row per product/warehouse/tenant; also serves as the primary lookup path for `WHERE tenant_id = ? AND product_id = ?` and fully-specified queries. |
| `INDEX (tenant_id, warehouse_id, product_id)` on `stock_levels` | Covers the reverse filter case — listing by `warehouse_id` without a `product_id` — which the first (leftmost-prefix) index cannot serve efficiently on its own.            |
| `UNIQUE (tenant_id, sku)` on `products` | Enforces SKU uniqueness per tenant and supports the `GET /api/v1/products/{sku}/history` lookup.                                                                             |
| Foreign key indexes on `product_id`, `warehouse_id`, `tenant_id` | Standard FK indexes created automatically via `foreignId()`; support join/lookup performance for movement history and reconciliation queries.                               |

Together, the two composite indexes on `stock_levels` ensure that `GET /api/v1/stock-levels`
can be filtered by `product_id` alone, `warehouse_id` alone, or both, while always using an
index rather than a table scan — this is what keeps the endpoint performant at the
1,000,000-row scale required by the task.

## Known Limitations

- **Pagination on `GET /api/v1/stock-levels` is offset-based** (Laravel's default
  `paginate()`). Under very deep pagination (large `OFFSET` values), MySQL must scan and
  discard all preceding rows before returning a page, which can approach or exceed the
  200ms budget at scale. Given more time, this endpoint would be migrated to
  **cursor-based pagination** (`cursorPaginate()`), which keeps performance constant
  regardless of how deep into the result set a client navigates, at the cost of not
  supporting "jump to page N" navigation. For the current dataset sizes tested, offset
  pagination stays within budget for realistic (non-deep) page requests.

## Tests

```bash
# Stock movement logic, including the concurrency test
make stock-movement-test

# Stock level API endpoints (listing, filtering, pagination)
make stock-level-test
```

`make stock-movement-test` runs `Modules/Stock/tests/Feature/StockMovementServiceTest.php`,
which includes the required concurrency test: 100 simultaneous `out` movements against a
single product with an initial stock of 50, dispatched as jobs to the dedicated
`stock-concurrency-test` Redis queue and processed by the `stock-test-worker` service so the
movements are genuinely processed in parallel rather than sequentially in one process. The
test asserts exactly 50 succeed, 50 fail with the domain exception, and the final stock
quantity is 0.

**Before running the concurrency test**, scale up the `stock-test-worker` service so enough
queue workers are available to process the 100 movements concurrently rather than one at a
time:

```bash
docker compose up -d --scale stock-test-worker=20
```

`stock-test-worker` runs `php artisan queue:work redis --queue=stock-concurrency-test
--sleep=0 --tries=1 --timeout=30` — a queue consumer dedicated to this test, kept separate
from the application's regular `queue` service so concurrency testing never competes with,
or is throttled by, normal application queue processing. Scaling it to ~20 replicas
provides enough parallel workers for the test to exercise genuine simultaneous writes
against the same `stock_levels` row.

`make stock-level-test` runs `Modules/Stock/tests/Feature/StockLevelTestApi.php`, covering
the `GET /api/v1/stock-levels` endpoint's filtering and pagination behavior.

## Reconciliation

```bash
php artisan inventory:reconcile {tenant}
```

Recomputes `stock_levels` from the full `stock_movements` history for the given tenant and
compares it against the currently stored values, logging any discrepancy (product,
warehouse, expected vs. actual quantity) without mutating data — safe to run repeatedly and
in production.
