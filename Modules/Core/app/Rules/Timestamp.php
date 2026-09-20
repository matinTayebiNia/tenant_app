<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class Timestamp implements ValidationRule
{
    public function __construct(protected string $attrTitle = '') {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = Validator::make([$attribute => $value], [
            $attribute => [
                'after_or_equal:1970-10-01 00:00:00',
                'before_or_equal:2037-10-19 03:14:17',
            ],
        ], attributes: [
            $attribute => $this->attrTitle ?: $attribute,
        ])->getMessageBag()->get($attribute);

        foreach ($message as $msg) {
            $fail(__($msg));
        }

    }
}
