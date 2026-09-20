<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Core\Enums\RegexPattern;

class ValidateRegex implements ValidationRule
{
    public function __construct(private RegexPattern $regexPattern) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match($this->regexPattern->value, $value)) {
            $fail(__('Core::regex_patterns.invalid_regex'));
        }
    }
}
