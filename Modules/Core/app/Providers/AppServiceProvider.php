<?php

namespace Modules\Core\Providers;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Enums\TimeFormats;
use Modules\Core\Helpers\ConfigHelper;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            Password::min(8)
                ->max(255);
        });
    }
}
