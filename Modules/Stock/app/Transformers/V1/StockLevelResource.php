<?php

namespace Modules\Stock\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\V1\ProductRelationResource;
use Modules\Tenant\Transformers\V1\TenantResource;

class StockLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant' => TenantResource::make($this->tenant),
            'product' => ProductRelationResource::make($this->product),
            'warehouse' => WarehouseRelationResource::make($this->warehouse),
            'quantity' => $this->quantity,
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
