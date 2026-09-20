<?php

namespace Modules\Core\ValueObjects;

use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Enums\Filters\FilterMethods;
use Modules\Core\Enums\Filters\FilterOperations;
use Modules\Core\Exceptions\BaseException;

class Filterable
{
    private const FILTER_ARRAY_OPERATION = [
        FilterOperations::Equal->value,
        FilterOperations::Range->value,
    ];

    protected array $queryMethods;

    protected array $operations;

    public array $values;

    protected array $fields;

    /**
     * @throws BaseException
     */
    protected function setMethod(mixed $value, $op): void
    {
        if (is_array($value) && $op === FilterOperations::Equal->value) {
            $this->queryMethods[] = FilterMethods::WhereIn->value;
        } elseif (is_array($value) && $op === FilterOperations::Range->value) {

            if (count($value) !== 2) {
                throw BaseException::new(
                    exceptionCode: ExceptionCode::ValidationException,
                    message: 'length of value must be 2'
                );
            }

            $this->queryMethods[] = FilterMethods::WhereBetween->value;

        } else {
            $this->queryMethods[] = FilterMethods::Where->value;
        }
    }

    /**
     * @throws BaseException
     */
    public function pushCondition(string $field, mixed $value, string $op = FilterOperations::Equal->value)
    {
        $this->fields[] = $field;
        $this->values[] = $value;
        $this->operations[] = $op;
        $this->setMethod($value, $op);

        return $this;
    }

    public function transfer(): array
    {
        return array_map(fn ($field, $val, $op, $method) => [
            'field' => $field,
            'value' => $val,

            'op' => in_array($op, self::FILTER_ARRAY_OPERATION) && is_array($val)
                ? null
                : $op,

            'method' => $method,
        ], $this->fields, $this->values, $this->operations, $this->queryMethods);
    }
}
