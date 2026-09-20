<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class DateTimeBetween implements ValidationRule
{
    private const FLAGS = [
        'DATE' => 'Y-m-d',
        'TIME' => 'H:i',
        'DATETIME' => 'Y-m-d H:i',
    ];

    public function __construct(
        protected ?string $start,
        protected ?string $end,
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
        if (! $value || ! $this->start || ! $this->end) {
            return;
        }

        $dateTimes = $this->parseToCarbon($this->start, $this->end, $value);

        if (! $dateTimes) {
            // $fail(__('Core::validations.invalid_datetime'));

            return;
        }

        extract($dateTimes);

        $format = self::FLAGS[$this->flag];

        if ($this->isOutOfRange($start, $end, $value, $format)) {
            $fail(__('Core::validations.datetime_between', [
                'start' => $start->format($format),
                'end' => $end->format($format),
            ]));
        }
    }

    private function parseToCarbon(string $start, string $end, string $value): array
    {
        try {
            return [
                'start' => Carbon::parse($start),
                'end' => Carbon::parse($end),
                'value' => Carbon::parse($value),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function isOutOfRange(Carbon $start, Carbon $end, Carbon $value, string $format): bool
    {
        return $start->format($format) > $value->format($format) ||
               $end->format($format) < $value->format($format);
    }
}
