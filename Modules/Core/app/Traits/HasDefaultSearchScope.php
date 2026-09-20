<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Helpers\Helper;
use Modules\Core\ValueObjects\ColumnFilter;
use Modules\Core\ValueObjects\RelationalFilter;
use Modules\Tenant\Helper\TenantScopeConfig;

/**
 * @method static Builder search(?string $searchQuery, array $columns);
 * @method static Builder relationalFilter(RelationalFilter ...$filters);
 */
trait HasDefaultSearchScope
{

    public function scopeSearch(
        Builder $query,
        ?string $searchQuery,
        array   $columns,
    ): Builder
    {
        if (!$searchQuery) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($columns, $searchQuery) {
            $this->applyBasicSearch($q, $columns, $searchQuery);
        });
    }

    protected function applyBasicSearch(Builder $query, array $columns, string $searchQuery): void
    {
        foreach ($columns as $column) {
            $query->orWhere($column, 'LIKE', "$searchQuery");
        }
    }

    public function scopeColumnFilter(Builder $builder, ColumnFilter ...$filters): void
    {
        foreach ($filters as $filter) {
            $this->applyFilterConditions($builder, $filter->transfer());
        }
    }

    public function scopeRelationalFilter(Builder $builder, RelationalFilter ...$filters): void
    {
        foreach ($filters as $filter) {
            $builder->whereHas($filter->relation, function ($query) use ($filter) {
                $this->applyFilterConditions($query, $filter->transfer());
            }, $filter->countOperator, $filter->count);
        }
    }

    protected function applyFilterConditions(Builder $builder, array $conditions): void
    {
        foreach ($conditions as $condition) {
            $method = $condition['method'];
            $field = $condition['field'];
            $value = $condition['value'];
            $operator = $condition['op'] ?? null;

            $operator
                ? $builder->$method($field, $operator, $value)
                : $builder->$method($field, $value);
        }
    }
}
