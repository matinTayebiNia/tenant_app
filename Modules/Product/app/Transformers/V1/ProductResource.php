<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Tenant\Transformers\V1\TenantResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'tenant' => TenantResource::make($this->tenant),
            'name' => $this->name,
            'sku' => $this->sku,
            'unit_price' => $this->unit_price,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
