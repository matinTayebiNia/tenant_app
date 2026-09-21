<?php

namespace Modules\Tenant\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Tenant\Enum\TenantScopeDataType;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\ValueObject\TenantScopeSetting;

/**
 * @method static  HasTenantScope(?Tenant $scope = null);
 */
trait HasTenantScope
{

    public static TenantScopeSetting $setting;


    public static function setSetting(?TenantScopeSetting $type = null): void
    {
        self::$setting = $type ?: static::setupTenantScope();
    }

    public static function bootHasTenantScope(): void
    {
        self::setSetting();
    }

    abstract public static function setupTenantScope(): TenantScopeSetting;

    public function scopeHasTenantScope(Builder $q, ?Tenant $scope = null)
    {
        $scope = $scope ?: TenantScopeConfig::getCurrent();

        $set = $q->getModel()::$setting;

        $whereHasTenantScope = $set->whereHasTenantScope($q, $scope);

        return match ($set->dataType) {

            TenantScopeDataType::SingleScope => $whereHasTenantScope ?: $q->where('tenant_id', $scope?->id),
            default => $q
        };
    }

    public function belongToTenantScope(?Tenant $tenant = null): bool
    {
        $tenant ??= TenantScopeConfig::getCurrent();

        $belongsToSiteScope = static::$setting->belongsToTenantScope($this, $tenant);

        return match (static::$setting->dataType) {
            TenantScopeDataType::SingleScope => is_null($belongsToSiteScope) && $this->tenant_id === $tenant->id,
        };
    }

    public function tenantScopes(): null|array|Tenant|Collection
    {
        $siteScopes = static::$setting->tenantScopes($this);

        return match (static::$setting->dataType) {
            TenantScopeDataType::SingleScope => $siteScopes ?? Tenant::find($this->tenant_id),
        };
    }

}
