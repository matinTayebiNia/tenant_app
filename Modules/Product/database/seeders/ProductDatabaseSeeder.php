<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Modules\Tenant\Models\Tenant;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trob=Tenant::query()->where('subdomain','trob')->first();
        $digikala=Tenant::query()->where('subdomain','digikala')->first();
        Product::factory()->count(20)->create([
            'tenant_id' =>$trob->id
        ]);
        Product::factory()->count(20)->create([
            'tenant_id' =>$digikala->id
        ]);
    }
}
