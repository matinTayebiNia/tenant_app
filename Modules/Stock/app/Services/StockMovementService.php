<?php

namespace Modules\Stock\Services;

use App\Modules\Product\Dto\V1\ProductFilterDto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;
use Modules\Stock\Dto\V1\StockMovementDto;
use Modules\Stock\Enums\StockType;
use Modules\Stock\Models\StockLevel;
use Modules\Stock\Models\StockMovement;
use Modules\Tenant\Helper\TenantScopeConfig;
use Throwable;

class StockMovementService
{


    public function getMovementBySku(
        IndexDTO         $dto,
        string           $sku,
        ProductFilterDto $filterDto,
        array            $relations = [],
        array            $columns = ['*']
    )
    {

        return StockMovement::query()
            ->with($relations)
            ->select($columns)
            ->whereRelation('product', 'sku', $sku)
            ->relationalFilter(...$filterDto->relationalFilters())
            ->relationalFilter(...$filterDto->columnFilters())
            ->index($dto);
    }

    /**
     * @throws Throwable
     * @throws BaseException
     */
    public function create(StockMovementDto $dto): bool
    {
        return match ($dto->type) {
            StockType::In => $this->handleIn($dto),
            StockType::Out => $this->handleOut($dto),
            StockType::Transfer => $this->handleTransfer($dto),
        };
    }

    /**
     * @throws Throwable
     * @throws BaseException
     */
    private function handleIn(StockMovementDto $dto): bool
    {
        try {
            return DB::transaction(function () use ($dto) {

                $stockLevel = StockLevel::query()
                    ->where('product_id', $dto->product_id)
                    ->where('warehouse_id', $dto->warehouse_id)
                    ->lockForUpdate()
                    ->HasTenantScope()
                    ->first();

                if (!$stockLevel) {
                    $stockLevel = StockLevel::query()->create([
                        'tenant_id' => TenantScopeConfig::getCurrent()?->id,
                        'product_id' => $dto->product_id,
                        'warehouse_id' => $dto->warehouse_id,
                        'quantity' => $dto->quantity,
                    ]);
                }

                $stockLevel->increment('quantity', $dto->quantity);

                $this->setMovement($dto);

                return true;
            });
        } catch (QueryException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                return $this->handleIn($dto);
            }
            throw BaseException::new(ExceptionCode::GenericInternalServerError);
        }

    }

    /**
     * @param StockMovementDto $dto
     * @param string|null $from
     * @param string|null $to
     * @return void
     * @throws BaseException
     */
    private function setMovement(StockMovementDto $dto, string $from = null, string $to = null): void
    {

        if ($dto->type == StockType::Transfer) {

            if (is_null($from) || is_null($to)) {
                throw BaseException::new(ExceptionCode::GenericInternalServerError);
            }

            $meta = [
                "from_warehouse_id" => $from,
                "to_warehouse_id" => $to,
            ];
        }
        StockMovement::query()->create([
            'tenant_id' => TenantScopeConfig::getCurrent()->id,
            'product_id' => $dto->product_id,
            'warehouse_id' => $dto->warehouse_id,
            'quantity' => $dto->quantity,
            'reference' => $dto->reference,
            'type' => $dto->type->value,
            'meta' => isset($meta) ? json_encode($meta) : null,
        ]);
    }

    /**
     * @throws Throwable
     */
    private function handleOut(StockMovementDto $dto)
    {
        return DB::transaction(function () use ($dto) {

            $stockLevel = StockLevel::query()
                ->where('product_id', $dto->product_id)
                ->where('warehouse_id', $dto->warehouse_id)
                ->lockForUpdate()
                ->HasTenantScope()
                ->firstOrFail();

            if ($stockLevel->quantity < $dto->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "The quantity of this product is out of stock.",
                ]);
            }

            $stockLevel->decrement('quantity', $dto->quantity);

            $this->setMovement($dto);

            return true;
        });
    }

    /**
     * @throws Throwable
     * @throws BaseException
     */
    private function handleTransfer(StockMovementDto $dto)
    {
        try {
            return DB::transaction(function () use ($dto) {

                $sourceStockLevel = StockLevel::query()
                    ->where('product_id', $dto->product_id)
                    ->where('warehouse_id', $dto->warehouse_id)
                    ->lockForUpdate()
                    ->HasTenantScope()
                    ->firstOrFail();

                if ($sourceStockLevel->quantity < $dto->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "The quantity of this product is out of stock.",
                    ]);
                }


                $sourceStockLevel->decrement('quantity', $dto->quantity);


                $destinationStockLevel = StockLevel::query()
                    ->where('product_id', $dto->product_id)
                    ->where('warehouse_id', $dto->destination_warehouse_id)
                    ->lockForUpdate()
                    ->HasTenantScope()
                    ->first();

                if (!$destinationStockLevel) {
                    $destinationStockLevel = StockLevel::query()->create([
                        'warehouse_id' => $dto->destination_warehouse_id,
                        'quantity' => $dto->quantity,
                        'product_id' => $dto->product_id,
                        'tenant_id' => TenantScopeConfig::getCurrent()?->id,
                    ]);
                }

                $destinationStockLevel->increment('quantity', $dto->quantity);

                $this->setMovement($dto, $sourceStockLevel->warehouse_id, $dto->destination_warehouse_id);

                return true;
            });
        } catch (QueryException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                return $this->handleTransfer($dto);
            }
            throw BaseException::new(ExceptionCode::GenericInternalServerError);
        }
    }


}
