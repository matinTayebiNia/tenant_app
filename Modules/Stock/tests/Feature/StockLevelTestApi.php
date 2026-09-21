<?php

namespace Modules\Stock\Tests\Feature;

use Modules\Stock\Models\StockLevel;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Models\Tenant;
use Tests\TestCase;

class StockLevelTestApi extends TestCase
{

    public function test_it_stock_level_duration_api_must_be_below_200_ms()
    {
        $tenant = Tenant::factory()->create();

        TenantScopeConfig::setCurrent($tenant);

        StockLevel::factory(500)->create([
            'tenant_id' => $tenant->id,
            'quantity' => 100,
        ]);

        $this->getJson("/api/v1/stock-levels", [
            'X-Tenant-Scope' => $tenant->subdomain,
        ])->assertSuccessful();

        $start = hrtime(true);

        $response = $this->getJson("/api/v1/stock-levels", [
            'X-Tenant-Scope' => $tenant->subdomain,
        ]);

        $durationMs = (hrtime(true) - $start) / 100_000;

        $response->assertSuccessful();

        $this->assertLessThan(
            200,
            $durationMs,
            "GET /api/v1/stock-levels took {$durationMs} ms"
        );

    }
}
