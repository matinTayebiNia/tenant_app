<?php

namespace Modules\Stock\Dto\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\DTOs\HandleFilterDTO;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\ValueObjects\RelationalFilter;

readonly class StockLevelFilterDto extends HandleFilterDTO
{

    public function __construct
    (
        public ?int $product_id = null,
        public ?int $warehouse_id = null,
    )
    {
    }

    public static function makeFromRequest(FormRequest $request): static
    {
        return new static(
            product_id: $request->validated('product_id'),
            warehouse_id: $request->validated('warehouse_id'),
        );
    }

    /**
     * @throws BaseException
     */
    protected function getRelationalObjects(): array
    {
        $res = [];

        if (isset($this->product_id) && $this->product_id) {
            $res[] = new RelationalFilter(
                'product', 'product_id', $this->product_id
            );
        }

        if (isset($this->warehouse_id) && $this->warehouse_id) {
            $res[] = new RelationalFilter(
                'warehouse', 'warehouse_id', $this->warehouse_id
            );
        }

        return $res;
    }

    protected function getColumnFields(): array
    {
        return [];
    }
}
