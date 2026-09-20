<?php

namespace Modules\Core\Macros;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Helpers\ConfigHelper;
use Modules\Core\Helpers\Helper;
use Modules\Tenant\Support\Traits\HasTenantScope;


final class IndexMacro
{
    public function __invoke(Builder $builder, ?IndexDTO $dto = null)
    {
        if (!$dto) {
            return $this->defaultResult($builder);
        }

        $this->applyColumns($builder, $dto);
        $this->applySorting($builder, $dto);
        $this->applySearch($builder, $dto);

        return $this->resolveResult($builder, $dto);
    }


    private function applyColumns(Builder $builder, IndexDTO $dto): void
    {
        if ($dto->columns) {
            $builder->select($dto->columns);
        }
    }

    private function applySorting(Builder $builder, IndexDTO $dto): void
    {
        $sortFields = $dto->sort_types ?: ConfigHelper::$sort_field;
        foreach ($sortFields as $field) {
            $builder->orderBy($field, $dto->sort);
        }
    }

    private function applySearch(Builder $builder, IndexDTO $dto): void
    {
        if ($dto->search) {
            $builder->search(
                $dto->search,
                $dto->searchFields,
            );
        }


        if ($dto->whit_tenant && Helper::hasTrait($builder->getModel(), HasTenantScope::class)) {
            $builder->hasTenantScope();
        }
    }

    private function resolveResult(Builder $builder, IndexDTO $dto)
    {

        if ($dto->take) {
            return $builder->take($dto->take)->get();
        }

        if ($dto->per_page) {
            return $builder->paginate($dto->per_page);
        }


        return $this->defaultResult($builder);
    }

    private function defaultResult(Builder $builder)
    {
        return $builder
            ->take(ConfigHelper::$limit_per_page)
            ->get();
    }
}
