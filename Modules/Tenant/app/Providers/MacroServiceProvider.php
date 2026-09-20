<?php

namespace Modules\Tenant\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blueprint::macro('tenantColumn', function ($column = 'tenant_id') {
            $this->foreignId($column)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
