<?php

namespace Modules\Stock\Dto\V1;

use Modules\Stock\Enums\StockType;

class StockMovementDto
{
    public function __construct(
        public int       $product_id,
        public int       $warehouse_id,
        public StockType $type,
        public int       $quantity,
        public int       $reference,
        public ?int      $destination_warehouse_id,
    )
    {
    }

    public static function make(array $data): StockMovementDto
    {
        return new self(
            product_id: $data['product_id'],
            warehouse_id: $data['warehouse_id'],
            type: StockType::tryFrom($data['type']),
            quantity: $data['quantity'],
            reference: $data['reference'],
            destination_warehouse_id: $data['destination_warehouse_id'],
        );
    }

}
