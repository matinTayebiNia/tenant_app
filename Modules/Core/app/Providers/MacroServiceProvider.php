<?php

namespace Modules\Core\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Enums\TimeFormats;
use Modules\Core\Macros\IndexMacro;

class MacroServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->builderIndex();
    }

    public function builderIndex(): void
    {
        Builder::macro('index', function (?IndexDTO $dto = null) {
            return app(IndexMacro::class)($this, $dto);
        });
    }


}
