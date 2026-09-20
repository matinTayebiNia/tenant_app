<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ModuleServiceProvider::class,
    App\Providers\NwidartCommandsProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    Jenssegers\Agent\AgentServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,
    Modules\Core\Providers\CoreServiceProvider::class,
    Modules\Log\Providers\LogServiceProvider::class,
    ...loadProviders()
];
