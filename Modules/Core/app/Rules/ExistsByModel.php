<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class ExistsByModel implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function __construct(protected Model $model, protected string $field = 'id') {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        ! ($this->model->where($this->field, $value)->first())
        && $fail(__('Core::validations.model-not-exists'));
    }
}
