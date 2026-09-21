<?php

namespace Modules\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Stock\Models\Warehouse;
use Modules\Tenant\Models\Tenant;

class WarehouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Warehouse::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName,
            'location' => $this->faker->streetAddress,
            'tenant_id' => Tenant::factory()->create()->getKey(),
        ];
    }
}

