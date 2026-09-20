<?php

namespace Modules\Log\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function register()
    {
        //        parent::register();
    }

    public function boot(): void
    {
        //        parent::boot();
    }

    public function map(): void
    {
        $this->mapLogV1Routes();
    }

    protected function mapLogV1Routes(): void
    {
        $res = array_map(fn ($file) => $file->getRealPath(),
            File::allFiles(module_paths('Log', '/routes/v1/'))
        );

        Route::middleware(['api'])
            ->prefix('api/v1')
            ->name('api.v1')
            ->group($res);
    }
}
