<?php

namespace Modules\Product\Services;

use App\Modules\Product\Dto\V1\ProductDto;
use Modules\Core\DTOs\IndexDTO;
use Modules\Product\Models\Product;

class ProductService
{

    public function index(IndexDTO $Dto, array $relations = [], array $columns = ['*'])
    {
        return Product::query()
            ->with($relations)
            ->select($columns)
            ->index($Dto);

    }

    public function store(ProductDto $dto): bool
    {
        return Product::create($dto->toArray());
    }

    public function update(ProductDto $dto, Product $product): bool
    {
        return $product->update($dto->toArray());
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

}
