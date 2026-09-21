<?php

namespace Modules\Tenant\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tenant\Models\Tenant;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tenant::query()
            ->insert(
               [
                   [
                       'name' => 'digikala',
                       'subdomain' => 'digikala',
                       'created_at' => now(),
                   ],
                   [
                       'name' => 'trob',
                       'subdomain' => 'trob',
                       'created_at' => now(),
                   ]
               ]
            );
    }
}
