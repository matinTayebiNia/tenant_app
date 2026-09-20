<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PersianChar implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $p = "/^[\x{0600}-\x{06FF}0-9\s+]*$/u";

        ! preg_match($p, $value) && $fail(__('Core::validation.use_persian_chars'));
    }
}
