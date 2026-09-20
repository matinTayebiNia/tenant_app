<?php

namespace App\Modules\Product\Dto\V1;

use Modules\Tenant\Helper\TenantScopeConfig;

readonly class ProductDto
{

    public function __construct(
        public string $sku,
        public string $name,
        public float  $unitPrice,
    )
    {

    }

    public static function make(array $data): ProductDto
    {
        return new self(
            sku: $data['sku'],
            name: $data['name'],
            unitPrice: $data['unitPrice'],
        );
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'unitPrice' => $this->unitPrice,
            'tenant_id' => TenantScopeConfig::getCurrent()->getKey()
        ];
    }


}
