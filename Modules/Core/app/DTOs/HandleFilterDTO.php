<?php

namespace Modules\Core\DTOs;

abstract readonly class HandleFilterDTO
{
    abstract protected function getRelationalObjects(): array;

    abstract protected function getColumnFields(): array;

    public function columnFilters(): array
    {
        return $this->getFilters();
    }

    public function relationalFilters(): array
    {
        return $this->getFilters(false);
    }

    private function getFilters(bool $isColFilter = true): array
    {
        $data = $isColFilter
            ? $this->getColumnFields()
            : $this->getRelationalObjects();

        $filters = [];

        foreach ($data as $relationalObject) {

            $filters[] = $relationalObject;
        }

        return $filters;
    }
}
