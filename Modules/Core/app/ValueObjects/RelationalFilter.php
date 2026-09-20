<?php

namespace Modules\Core\ValueObjects;

use Illuminate\Support\Arr;
use Modules\Core\Enums\Filters\FilterOperations;
use Modules\Core\Exceptions\BaseException;

class RelationalFilter extends Filterable
{
    /**
     * @throws BaseException
     */
    public function __construct(
        public string $relation,
        string $field,
        mixed $value,
        string $op = FilterOperations::Equal->value,
        public string $countOperator = '>=',
        public int $count = 1,
    ) {
        $this->fields = Arr::wrap($field);
        $this->values = is_array($value) ? [$value] : Arr::wrap($value);
        $this->operations = Arr::wrap($op);
        $this->setMethod($value, $op);
    }
}
