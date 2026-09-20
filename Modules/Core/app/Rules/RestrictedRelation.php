<?php

namespace Modules\Core\Rules;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class  RestrictedRelation implements ValidationRule
{
    public function __construct(
        protected ?BackedEnum $activeStatus,
        protected ?Model      $model = null,
        protected string      $errorMsg = ''
    )
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->model || $value == $this->activeStatus?->value /* || $this->model->status?->value == $value */ )
            return;

        $this->model->hasRestrictedRelations()
        &&
        $fail($this->errorMsg ?: $this->model->restrictedErrMsg());

    }

}
