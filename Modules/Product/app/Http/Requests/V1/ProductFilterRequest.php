<?php

namespace Modules\Product\Http\Requests\V1;

use Modules\Core\Http\Requests\IndexRequest;
use Modules\Product\Models\Product;
use Modules\Stock\Models\Warehouse;
use Modules\Tenant\Rules\BelongToTenant;

class ProductFilterRequest extends IndexRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'product_id' => ['sometimes', 'integer', new BelongToTenant(Product::class), 'max:' . PHP_INT_MAX],
            'warehouse_id' => ['sometimes', 'integer', new BelongToTenant(Warehouse::class), 'max:' . PHP_INT_MAX],
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
