<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShouldUnique implements ValidationRule
{
    public function __construct(
        protected $model,
        protected ?string $attrTitle = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $messages = Validator::make([$attribute => $value], [
            $attribute => [
                Rule::unique($this->model->getTable(), $attribute)
                    ->withoutTrashed()
                    ->ignoreModel($this->model),
            ],
        ],
            attributes: [
                $attribute => $this->attrTitle ?: $attribute,
            ]
        )->getMessageBag()->get($attribute);

        foreach ($messages as $msg) {
            $fail($msg);
        }

    }
}
