#!/usr/bin/env bash
set -e

# Shared bootstrap for every PHP-based container (web, queue, scheduler, migrate).
# Cheap and idempotent — safe to run on every container start.

[ -L public/storage ] || php artisan storage:link

# Deliberately NOT running config:cache here. It freezes every config value (not just
# APP_ENV) using whatever the container's real environment is at start time — and once
# frozen, nothing at request/command time can override it, including phpunit.xml's testing
# overrides. That makes `php artisan test` silently run against the real database/session
# config instead of an isolated one, inside the very same container that's meant to run it.
# This is a demo/assessment app, not a high-traffic production service, and Octane's
# persistent workers already avoid re-bootstrapping the framework per request — the marginal
# gain from also caching config is not worth reintroducing that failure mode.
php artisan route:cache
php artisan view:cache

exec "$@"
