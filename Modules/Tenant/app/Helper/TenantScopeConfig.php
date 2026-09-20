<?php

namespace Modules\Tenant\Helper;

use Illuminate\Support\Facades\Config;
use Modules\Tenant\Models\Tenant;

class TenantScopeConfig
{
    const string CURRENT_SCOPE = 'tenant-scope.current';

    public static function setCurrent(Tenant $type): void
    {
        Config::set(self::CURRENT_SCOPE,$type);
    }

    public static function getCurrent(): ?Tenant
    {
        return Config::get(self::CURRENT_SCOPE);
    }

}
