<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Product\Models\Product;
use Modules\Tenant\Helper\TenantScopeConfig;

class UniqueSku implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $condition = Product::query()->where('sku', $value)
            ->where('tenant_id', TenantScopeConfig::getCurrent()?->id)
            ->exists();

        if($condition){
            $fail('The sku has already been taken.');
        }
    }
}
