<?php

namespace Modules\Core\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Modules\Core\Helpers\ConfigHelper;
use Modules\Tenant\Helper\TenantScopeConfig;
use Ramsey\Collection\Sort;

readonly class IndexDTO
{
    public null|array|string $sort_types;

    public function __construct(
        public ?int       $per_page = null,
        public ?int       $take = null,
        public ?string    $search = null,
        public ?string    $sort = Sort::Descending->value,
        null|array|string $sort_types = null,
        public ?array     $searchFields = ['title', 'id'],
        public ?array     $columns = null,
        public bool      $whit_tenant = true
    )
    {
        $this->sort_types = $sort_types ? Arr::wrap($sort_types) : ConfigHelper::$sort_field;
    }

    public static function makeFromRequest(
        FormRequest       $request,
        ?array            $searchFields = ['title', 'id'],
        bool              $disablePaginate = false,
        ?array            $columns = null,
        null|array|string $sort_types = null,
        ?array            $fulltextColumns = [],
        ?int              $take = null,
        bool             $withTenant = true

    ): IndexDTO
    {
        return new self(
            per_page: $disablePaginate ? null : ($request->validated('per_page') ?? ConfigHelper::$per_page),
            take: $take ?? $request->validated('take'),
            search: $request->validated('search'),
            sort: $request->validated('sort') ?? Sort::Descending->value,
            sort_types: $sort_types ?? $request->validated('sort_type'),
            searchFields: $searchFields,
            columns: $columns,
            whit_tenant: $withTenant
        );
    }

    public function toArray(): array
    {
        return [
            'per_page' => $this->per_page,
            'take' => $this->take,
            'search' => $this->search,
            'sort' => $this->sort,
        ];
    }
}
