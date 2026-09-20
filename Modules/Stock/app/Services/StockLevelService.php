<?php

namespace Modules\Stock\Services;

use Modules\Core\DTOs\IndexDTO;
use Modules\Stock\Dto\V1\StockLevelFilterDto;
use Modules\Stock\Models\StockLevel;

class StockLevelService
{
    public function index(IndexDTO $dto, StockLevelFilterDto $filterDto, array $relations = [], array $columns = ['*'])
    {
        return StockLevel::query()
            ->with($relations)
            ->select($columns)
            ->relationalFilter(...$filterDto->relationalFilters())
            ->relationalFilter(...$filterDto->columnFilters())
            ->index($dto);
    }
}
