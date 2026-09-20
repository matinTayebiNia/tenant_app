<?php

namespace Modules\Tenant\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Tenant\Support\Http\Middleware\DetectTenantScopeFromHeader;
use Modules\Tenant\Support\Http\Middleware\DetectTenantScopeFromSubDomain;
use Modules\Tenant\Support\Http\Middleware\ModelBelongsToScope;

class RouteServiceProvider extends ServiceProvider
{


    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->handleMiddleware();

        $this->mapApiV1Routes();

    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiV1Routes(): void
    {
        $v1Path = module_paths('Tenant', '/routes/v1');
        $v1Files = array_diff(scandir($v1Path), ['..', '.']);

        Route::middleware(['api'])
            ->prefix('api/v1/')
            ->name('api.v1.')
            ->group(array_map(fn ($val) => $v1Path.'/'.$val, $v1Files));
    }

    private function handleMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('modelBelongsToScope', ModelBelongsToScope::class);

        $this->app['router']->middlewareGroup('tenant-scope', [
            DetectTenantScopeFromHeader::class,
            DetectTenantScopeFromSubDomain::class
        ]);

    }
}
