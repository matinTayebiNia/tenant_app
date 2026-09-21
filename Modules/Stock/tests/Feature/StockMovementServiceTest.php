<?php

namespace Modules\Stock\Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Core\Exceptions\BaseException;
use Modules\Stock\Dto\V1\StockMovementDto;
use Modules\Stock\Enums\StockType;
use Modules\Stock\Jobs\ConcurrentStockOutJob;
use Modules\Stock\Models\StockLevel;
use Modules\Stock\Models\StockMovement;
use Modules\Stock\Models\Warehouse;
use Modules\Stock\Services\StockMovementService;
use Modules\Tenant\Helper\TenantScopeConfig;
use Tests\TestCase;

class StockMovementServiceTest extends TestCase
{

    /**
     * @throws \Throwable
     * @throws BaseException
     */
    public function test_it_can_move_stock_out(): void
    {
        $stock = StockLevel::factory()->create([
            'quantity' => 50,
        ]);

        $tenant = $stock->tenant;

        TenantScopeConfig::setCurrent($tenant);

        $service = app(StockMovementService::class);

        $service->create(
            new StockMovementDto(
                product_id: $stock->product_id,
                warehouse_id: $stock->warehouse_id,
                type: StockType::Out,
                quantity: 1,
                reference: "ABC-123",
            )
        );


        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 49,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenant->id,
            'type' => StockType::Out->value,
            'product_id' => $stock->product_id,
            'warehouse_id' => $stock->warehouse_id,
            'quantity' => 1,
            'reference' => 'ABC-123',
        ]);
    }

    public function test_it_can_move_stock_in()
    {

        $stock = StockLevel::factory()->create([
            'quantity' => 1,
        ]);

        $tenant = $stock->tenant;

        TenantScopeConfig::setCurrent($tenant);

        $service = app(StockMovementService::class);

        $service->create(
            new StockMovementDto(
                product_id: $stock->product_id,
                warehouse_id: $stock->warehouse_id,
                type: StockType::In,
                quantity: 50,
                reference: "ABC-123",
            )
        );

        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 51,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenant->id,
            'type' => StockType::In->value,
            'product_id' => $stock->product_id,
            'warehouse_id' => $stock->warehouse_id,
            'quantity' => 50,
            'reference' => 'ABC-123',
        ]);
    }

    public function test_it_can_transfer_stock_and_destination_not_exists()
    {
        $stock = StockLevel::factory()->create([
            'quantity' => 10,
        ]);

        $tenant = $stock->tenant;

        $destinationWarehouse = Warehouse::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        StockLevel::query()
            ->where('product_id', $stock->product_id)
            ->where('warehouse_id', $destinationWarehouse->id)
            ->where('tenant_id', $tenant->id)
            ->delete();

        TenantScopeConfig::setCurrent($tenant);

        $service = app(StockMovementService::class);

        $service->create(
            new StockMovementDto(
                product_id: $stock->product_id,
                warehouse_id: $stock->warehouse_id,
                type: StockType::Transfer,
                quantity: 5,
                reference: "ABC-123",
                destination_warehouse_id: $destinationWarehouse->id,
            )
        );

        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'product_id' => $stock->product_id,
            'warehouse_id' => $destinationWarehouse->id,
            'tenant_id' => $tenant->id,
            'quantity' => 5,
        ]);

        $movement = StockMovement::query()
            ->where('tenant_id', $tenant->id)
            ->where('type', StockType::Transfer->value)
            ->where('product_id', $stock->product_id)
            ->where('warehouse_id', $stock->warehouse_id)
            ->first();

        $this->assertNotNull($movement);

        $this->assertEquals([
            'to_warehouse_id' => $destinationWarehouse->id,
            'from_warehouse_id' => $stock->warehouse_id
        ], $movement->meta);

    }

    public function test_it_can_transfer_stock_and_destination_exists()
    {
        $stock = StockLevel::factory()->create([
            'quantity' => 10,
        ]);

        $tenant = $stock->tenant;

        $destinationWarehouse = Warehouse::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        StockLevel::query()
            ->updateOrCreate([
                'product_id' => $stock->product_id,
                'warehouse_id' => $destinationWarehouse->id,
                'tenant_id' => $tenant->id,
            ], [
                'quantity' => 5,
            ]);

        TenantScopeConfig::setCurrent($tenant);

        $service = app(StockMovementService::class);

        $service->create(
            new StockMovementDto(
                product_id: $stock->product_id,
                warehouse_id: $stock->warehouse_id,
                type: StockType::Transfer,
                quantity: 5,
                reference: "ABC-123",
                destination_warehouse_id: $destinationWarehouse->id,
            )
        );

        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'product_id' => $stock->product_id,
            'warehouse_id' => $destinationWarehouse->id,
            'tenant_id' => $tenant->id,
            'quantity' => 10,
        ]);

        $movement = StockMovement::query()
            ->where('tenant_id', $tenant->id)
            ->where('type', StockType::Transfer->value)
            ->where('product_id', $stock->product_id)
            ->where('warehouse_id', $stock->warehouse_id)
            ->first();

        $this->assertNotNull($movement);

        $this->assertEquals([
            'to_warehouse_id' => $destinationWarehouse->id,
            'from_warehouse_id' => $stock->warehouse_id
        ], $movement->meta);

    }

    public function test_it_cannot_move_out_more_than_available_stock(): void
    {
        $stock = StockLevel::factory()->create([
            'quantity' => 5,
        ]);

        $tenant = $stock->tenant;

        TenantScopeConfig::setCurrent($tenant);

        $service = app(StockMovementService::class);

        $this->expectException(ValidationException::class);

        $service->create(
            new StockMovementDto(
                product_id: $stock->product_id,
                warehouse_id: $stock->warehouse_id,
                type: StockType::Out,
                quantity: 6,
                reference: 'ABC-123',
            )
        );

        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 5,
        ]);
    }

    public function test_concurrent_stock_out_allows_only_available_stock(): void
    {
        config()->set('queue.default', 'redis');

        $stock = StockLevel::factory()->create([
            'quantity' => 50,
        ]);

        $tenant = $stock->tenant;

        TenantScopeConfig::setCurrent($tenant);

        $resultKey = 'stock-concurrency-test:' . Str::uuid();

        $redis = Redis::connection();

        $redis->del([
            "{$resultKey}:success",
            "{$resultKey}:failed",
        ]);

        for ($i = 0; $i < 100; $i++) {
            ConcurrentStockOutJob::dispatch(
                tenantId: $tenant->id,
                productId: $stock->product_id,
                warehouseId: $stock->warehouse_id,
                reference: "CONCURRENT-{$i}",
                resultKey: $resultKey,
            );
        }

        $this->waitForConcurrentJobs(
            redis: $redis,
            resultKey: $resultKey,
            expected: 100,
        );

        $this->assertSame(
            50,
            (int) $redis->get("{$resultKey}:success")
        );

        $this->assertSame(
            50,
            (int) $redis->get("{$resultKey}:failed")
        );

        $this->assertDatabaseHas('stock_levels', [
            'id' => $stock->id,
            'quantity' => 0,
        ]);
    }

    private function waitForConcurrentJobs(
        $redis,
        string $resultKey,
        int $expected,
    ): void {
        $timeout = microtime(true) + 30;

        while (microtime(true) < $timeout) {
            $success = (int) $redis->get("{$resultKey}:success");
            $failed = (int) $redis->get("{$resultKey}:failed");

            if (($success + $failed) === $expected) {
                return;
            }

            usleep(100_000);
        }

        $this->fail('Timed out waiting for concurrent jobs.');
    }
}
