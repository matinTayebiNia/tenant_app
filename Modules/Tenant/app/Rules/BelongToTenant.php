<?php

namespace Modules\Tenant\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Support\Traits\HasTenantScope;

class BelongToTenant implements ValidationRule
{
    public function __construct(
        private string $class,
    )
    {
    }

    /**
     * Run the validation rule.
     * @throws BaseException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!class_exists($this->class)) {
            throw BaseException::new(ExceptionCode::GenericInternalServerError);
        }
        /**
         * @var HasTenantScope $instance
         */
        $instance = $this->class::where('id', $value)->first();

        if(!$instance){
            $fail("The $attribute \"$value\" does not exist");
        }

        if (!$instance->belongToTenantScope()) {
            $fail("The $attribute \"$value\" does not exist");
        }

    }
}
