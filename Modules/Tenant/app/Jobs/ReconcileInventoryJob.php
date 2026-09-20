<?php

namespace Modules\Tenant\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Stock\Enums\StockType;
use Modules\Stock\Models\StockLevel;
use Modules\Stock\Models\StockMovement;
use Throwable;

class ReconcileInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public int $batchSize = 1000;

    public function __construct(
        private readonly int $tenant,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $deltas = [];

            /*
             * Read ALL movements for this tenant.
             *
             * We use chunkById because this is a full reconciliation
             * and we don't want to load all movements into memory.
             */
            StockMovement::query()
                ->where('tenant_id', $this->tenant)
                ->orderBy('id')
                ->chunkById(
                    $this->batchSize,
                    function ($movements) use (&$deltas) {

                        foreach ($movements as $movement) {

                            $productId = $movement->product_id;
                            $warehouseId = $movement->warehouse_id;
                            $quantity = $movement->quantity;

                            /*
                             * IN
                             *
                             * warehouse += quantity
                             */
                            if ($movement->type === StockType::In) {

                                $this->addDelta(
                                    $deltas,
                                    $productId,
                                    $warehouseId,
                                    $quantity
                                );

                                continue;
                            }

                            /*
                             * OUT
                             *
                             * warehouse -= quantity
                             */
                            if ($movement->type === StockType::Out) {

                                $this->addDelta(
                                    $deltas,
                                    $productId,
                                    $warehouseId,
                                    -$quantity
                                );

                                continue;
                            }

                            /*
                             * TRANSFER
                             *
                             * source      -= quantity
                             * destination += quantity
                             */
                            if ($movement->type === StockType::Transfer) {

                                $toWarehouseId = $movement->meta['to_warehouse_id'] ?? null;

                                if ($toWarehouseId === null) {
                                    throw new \RuntimeException(
                                        "Transfer movement {$movement->id} has no to_warehouse_id."
                                    );
                                }

                                // Source
                                $this->addDelta(
                                    $deltas,
                                    $productId,
                                    $warehouseId,
                                    -$quantity
                                );

                                // Destination
                                $this->addDelta(
                                    $deltas,
                                    $productId,
                                    (int)$toWarehouseId,
                                    $quantity
                                );
                            }
                        }
                    },
                    'id'
                );

            /*
             * Now $deltas contains the final calculated quantity
             * for every product + warehouse combination.
             */
            $this->updateStockLevels($deltas);
        });
    }

    private function addDelta(
        array     &$deltas,
        int       $productId,
        int       $warehouseId,
        int|float $quantity,
    ): void
    {
        $key = "{$productId}:{$warehouseId}";

        if (!isset($deltas[$key])) {
            $deltas[$key] = [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => 0,
            ];
        }

        $deltas[$key]['quantity'] += $quantity;
    }

    private function updateStockLevels(array $deltas): void
    {
        ksort($deltas);

        $stockLevels = StockLevel::query()
            ->where('tenant_id', $this->tenant)
            ->lockForUpdate()
            ->get();

        foreach ($stockLevels as $stockLevel) {

            $key = "{$stockLevel->product_id}:{$stockLevel->warehouse_id}";

            $expected = $deltas[$key]['quantity'] ?? 0;
            $actual = $stockLevel->quantity;

            if ($actual != $expected) {

                activity('inventory_discrepancy')
                    ->event('discrepancy')
                    ->performedOn($stockLevel)
                    ->causedBy($stockLevel)
                    ->withProperties([
                        'tenant_id' => $this->tenant,
                        'product_id' => $stockLevel->product_id,
                        'warehouse_id' => $stockLevel->warehouse_id,
                        'expected' => $expected,
                        'actual' => $actual,
                    ])->log('Inventory discrepancy detected.');

                $stockLevel->update([
                    'quantity' => $expected,
                ]);
            }

            unset($deltas[$key]);
        }

        /*
         * Remaining deltas are product/warehouse combinations
         * that have movements but don't have a StockLevel yet.
         */
        foreach ($deltas as $delta) {


            activity('inventory_discrepancy')
                ->event('discrepancy')
                ->withProperties([
                    'subject_type' => StockLevel::class,
                    'subject_id' => null,
                    'tenant_id' => $this->tenant,
                    'product_id' => $delta['product_id'],
                    'warehouse_id' => $delta['warehouse_id'],
                    'expected' => $delta['quantity'],
                    'actual' => null,
                ])->log('Inventory discrepancy detected.');

            StockLevel::query()->create([
                'tenant_id' => $this->tenant,
                'product_id' => $delta['product_id'],
                'warehouse_id' => $delta['warehouse_id'],
                'quantity' => $delta['quantity'],
            ]);

        }
    }
}
