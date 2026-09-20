<?php

namespace Modules\Stock\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Models\Product;
use Modules\Stock\Enums\StockType;
use Modules\Stock\Models\Warehouse;
use Modules\Tenant\Rules\BelongToTenant;

class StockMovementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'max:' . PHP_INT_MAX, new BelongToTenant(Product::class)],
            'warehouse_id' => ['required', 'integer', 'max:' . PHP_INT_MAX, new BelongToTenant(Warehouse::class)],
            'type' => ['required', 'string', Rule::enum(StockType::class)],
            'destination_warehouse_id' => [Rule::requiredIf($this->type == StockType::Transfer->value), 'integer', 'max:' . PHP_INT_MAX,
                new BelongToTenant(Warehouse::class)],
            'reference' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:' . PHP_INT_MAX],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
