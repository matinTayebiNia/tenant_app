<?php

namespace Modules\Stock\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\V1\ProductRelationResource;
use Modules\Tenant\Transformers\V1\TenantResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'tenant' => TenantResource::make($this->tenant),
            'product' => ProductRelationResource::make($this->product),
            'warehouse' => WarehouseRelationResource::make($this->warehouse),
            'reference' => $this->reference,
            'type' => StockTypeResource::make($this->type),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
