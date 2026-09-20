<?php

namespace Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Middleware\RestrictedRelation;

class RouteServiceProvider extends ServiceProvider
{
    private static $middlewareAlias = [
        'restricted_relation' => RestrictedRelation::class
    ];

    public function register()
    {
//       parent::register();
    }

    public function boot(): void
    {
        foreach (static::$middlewareAlias as $alias => $mw) {
            $this->app['router']->aliasMiddleware($alias, $mw);
        }
//        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapCoreV1ApiRoutes();

        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(module_paths('Core', '/routes/web.php'));
    }

    protected function mapCoreV1ApiRoutes(): void
    {
        $v1Path = module_paths('Core', '/routes/v1');
        $v1Files = array_diff(scandir($v1Path), ['..', '.']);

        Route::middleware(['api'])
            ->prefix('api/v1/')
            ->name('api.v1.')
            ->group(array_map(fn ($val) => $v1Path.'/'.$val, $v1Files));

    }
}
