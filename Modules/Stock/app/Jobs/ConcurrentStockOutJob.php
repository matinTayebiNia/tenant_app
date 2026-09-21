<?php

namespace Modules\Stock\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Modules\Stock\Dto\V1\StockMovementDto;
use Modules\Stock\Enums\StockType;
use Modules\Stock\Services\StockMovementService;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Models\Tenant;

class ConcurrentStockOutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int    $tenantId,
        public int    $productId,
        public int    $warehouseId,
        public string $reference,
        public string $resultKey,
    )
    {
        $this->onQueue('stock-concurrency-test');
    }


    public function handle(StockMovementService $service): void
    {
        $tenant = Tenant::findOrFail($this->tenantId);

        TenantScopeConfig::setCurrent($tenant);

        try {
            $service->create(
                new StockMovementDto(
                    product_id: $this->productId,
                    warehouse_id: $this->warehouseId,
                    type: StockType::Out,
                    quantity: 1,
                    reference: $this->reference,
                )
            );

            Redis::connection()->incr(
                "{$this->resultKey}:success"
            );
        } catch (ValidationException) {
            Redis::connection()->incr(
                "{$this->resultKey}:failed"
            );
        }
    }
}
