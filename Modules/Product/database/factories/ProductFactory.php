<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Models\Product;
use Modules\Tenant\Models\Tenant;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'sku' => $this->faker->unique()->slug(),
            'unit_price' => $this->faker->numberBetween(1000, 10000),
            'tenant_id' => Tenant::factory()->create()->id,
        ];
    }
}

