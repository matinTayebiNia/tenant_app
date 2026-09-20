<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class Text implements ValidationRule
{
    public function __construct(
        protected string $attrTitle = '',
        protected int $min = 0) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = Validator::make([$attribute => $value], [
            $attribute => [
                'string',
                'min:'.$this->min,
                'max:'.pow(2, 16) - 1,
            ],
        ], attributes: [
            $attribute => $this->attrTitle ?: $attribute,
        ])->getMessageBag()->get($attribute);

        foreach ($message as $msg) {
            $fail(__($msg));
        }
    }
}
