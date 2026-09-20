<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class DateTimeEqualTo implements ValidationRule
{
    private const FLAGS = [
        'DATE' => 'Y-m-d',
        'TIME' => 'H:i',
        'DATETIME' => 'Y-m-d H:i',
    ];

    public function __construct(
        protected ?string $dateTime,
        protected string $flag = 'DATETIME'
    ) {
        if (! array_key_exists($flag, self::FLAGS)) {
            throw new \InvalidArgumentException('Invalid flag');
        }
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value || !$this->dateTime) {
            return;
        }

        $dateTimes = $this->parseToCarbon($this->dateTime, $value);

        if(!$dateTimes) {
            // $fail(__('Core::validations.invalid_datetime'));
            return;
        }

        extract($dateTimes);

        $format = self::FLAGS[$this->flag];

        if ($dateTime->format($format) !== $value->format($format)) {
            $fail(__('Core::validations.datetime_equal_to', [
                'dateTime' => $dateTime->format($format),
            ]));
        }
    }

    private function parseToCarbon(string $dateTime, string $value): array
    {
        try {
            return [
                'dateTime' => Carbon::parse($dateTime),
                'value' => Carbon::parse($value),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
