<?php

namespace Modules\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Models\Product;
use Modules\Stock\Models\StockLevel;
use Modules\Stock\Models\Warehouse;
use Modules\Tenant\Models\Tenant;

class StockLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = StockLevel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $tenant = Tenant::factory()->create();

        return [
            'product_id' => Product::factory()->create([
                'tenant_id' => $tenant->getKey(),
            ])->getKey(),
            'warehouse_id' => Warehouse::factory()->create([
                'tenant_id' => $tenant->getKey(),
            ])->getKey(),
            'tenant_id' => $tenant->getKey(),
        ];
    }
}

