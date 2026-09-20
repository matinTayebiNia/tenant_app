<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Modules\Core\Helpers\ConfigHelper;

class ValidAge implements ValidationRule
{
    private int $minAge;

    private int $maxAge;

    public function __construct(
    ) {
        $this->minAge = ConfigHelper::$min_age;
        $this->maxAge = ConfigHelper::$max_age;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $age = Carbon::parse($value)->age;

        if ($age < $this->minAge || $age > $this->maxAge) {
            $fail(__('Core::validations.age_not_valid'));
        }
    }
}
