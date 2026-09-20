<?php

namespace Modules\Stock\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
        $this->mapApiV1Routes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiV1Routes(): void
    {
        $v1Path = module_paths('Stock', '/routes/v1');
        $v1Files = array_diff(scandir($v1Path), ['..', '.']);

        Route::middleware(['api'])
            ->prefix('api/v1/')
            ->name('api.v1.')
            ->group(array_map(fn ($val) => $v1Path.'/'.$val, $v1Files));
    }
}
