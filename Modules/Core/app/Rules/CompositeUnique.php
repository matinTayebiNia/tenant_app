<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class CompositeUnique implements ValidationRule
{

    public function __construct(
        protected string $model,
        protected array $columns,
        protected ?string $ignoreValue = null,
        protected ?string $ignoreColumn = 'id'
    )
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = $this->model::query();


        foreach ($this->columns as $column) {
            $query->where($column, $value);
        }


        if ($this->ignoreValue !== null) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreValue);
        }

        if ($query->count() > 0) {
            $fail('The :attribute must be unique.');
        }
    }
}


