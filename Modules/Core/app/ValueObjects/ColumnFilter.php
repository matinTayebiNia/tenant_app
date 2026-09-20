<?php

namespace Modules\Core\ValueObjects;

use Illuminate\Support\Arr;
use Modules\Core\Enums\Filters\FilterOperations;
use Modules\Core\Exceptions\BaseException;

class ColumnFilter extends Filterable
{
    /**
     * @throws BaseException
     */
    public function __construct(
        string $field,
        mixed $value,
        string $operator = FilterOperations::Equal->value,

    ) {

        $this->fields = Arr::wrap($field);
        $this->values = is_array($value) ? [$value] : Arr::wrap($value);
        $this->operations = Arr::wrap($operator);
        $this->setMethod($value, $operator);
    }
}
