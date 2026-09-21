<?php

namespace Modules\Stock\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Stock\Models\Warehouse;
use Modules\Tenant\Models\Tenant;

class StockDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trob=Tenant::query()->where('subdomain','trob')->first();
        $digikala=Tenant::query()->where('subdomain','digikala')->first();
        Warehouse::factory()->count(20)->create([
            'tenant_id' =>$trob->id
        ]);
        Warehouse::factory()->count(20)->create([
            'tenant_id' =>$digikala->id
        ]);    }
}
