<?php

namespace Modules\Tenant\ValueObject;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Tenant\Enum\TenantScopeDataType;
use Modules\Tenant\Models\Tenant;

class TenantScopeSetting
{
    public mixed $scopeDetector;

    protected function __construct(public TenantScopeDataType $dataType)
    {
        $this->scopeDetector['index'] =
        $this->scopeDetector['check'] =
        $this->scopeDetector['get'] = fn() => null;
    }

    public static function create(TenantScopeDataType $dataType): TenantScopeSetting
    {
        return new self($dataType);
    }

    public static function singleScope(): TenantScopeSetting
    {
        return new self(TenantScopeDataType::SingleScope);
    }

    public function setIndexAction(callable $index): static
    {
        $this->scopeDetector['index'] = $index;

        return $this;
    }

    public function setCheckAction(callable $check): static
    {
        $this->scopeDetector['check'] = $check;

        return $this;
    }

    public function setGettingAction(callable $get): static
    {
        $this->scopeDetector['get'] = $get;

        return $this;
    }

    public function whereHasTenantScope(Builder $q, Tenant $scope)
    {
        return $this->scopeDetector['index']($q, $scope);
    }

    public function belongsToTenantScope(Model $model, Tenant $scope)
    {
        return $this->scopeDetector['check']($model, $scope);
    }

    public function tenantScopes(Model $model)
    {
        return $this->scopeDetector['get']($model);
    }
}
