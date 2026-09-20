<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PublishedAt implements ValidationRule
{
    public function __construct(protected \BackedEnum $scheduledStatus, protected ?int $inputStatus) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = Validator::make([$attribute => $value], [
            $attribute => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => $this->inputStatus == $this->scheduledStatus->value),
                Rule::when($this->inputStatus != $this->scheduledStatus->value, [Rule::in([null])]),
                new Timestamp(__('Core::validations.attr.published_at')),
                'after_or_equal:now',
            ],
        ])->getMessageBag()->get($attribute);

        foreach ($message as $msg) {
            $fail($msg);
        }
    }
}
