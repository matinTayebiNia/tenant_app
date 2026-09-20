<?php

namespace Modules\Product\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Modules\Product\Dto\V1\ProductDto;
use App\Modules\Product\Dto\V1\ProductFilterDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Enums\CrudOperation;
use Modules\Core\Http\Requests\IndexRequest;
use Modules\Product\Http\Requests\V1\ProductFilterRequest;
use Modules\Product\Http\Requests\V1\ProductRequest;
use Modules\Product\Models\Product;
use Modules\Product\Services\ProductService;
use Modules\Product\Transformers\V1\ProductResource;
use Modules\Stock\Models\StockMovement;
use Modules\Stock\Services\StockMovementService;
use Modules\Stock\Transformers\V1\StockMovementResource;

class ProductController extends Controller
{

    public function __construct(
        private ProductService       $service,
        private StockMovementService $movementService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $data = $this->service->index(
            IndexDTO::makeFromRequest($request, Product::SEARCH_FIELDS),
            ['tenant']
        );

        return Response::success(
            data: ProductResource::collection($data),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        $this->service->store(
            ProductDto::make($request->validated())
        );

        return Response::crudSuccess(
            model: "محصول",
            crudOperation: CrudOperation::STORE
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(Product $product)
    {
        return Response::success(
            data: ProductResource::make($product),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->service->update(
            ProductDto::make($request->validated()),
            $product
        );

        return Response::crudSuccess(
            model: "محصول",
            crudOperation: CrudOperation::UPDATE
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

        $this->service->delete($product);

        return Response::crudSuccess(
            model: "محصول",
            crudOperation: CrudOperation::DESTROY
        );
    }

    public function history(ProductFilterRequest $request, string $sku)
    {
        $data = $this->movementService->getMovementBySku(
            IndexDTO::makeFromRequest($request, StockMovement::SEARCH_FIELDS),
            $sku,
            ProductFilterDto::makeFromRequest($request),
            [
                'product' => fn($q) => $q->select(['name', 'sku', 'id']),
                'warehouse' => fn($q) => $q->select(['name', 'id']),
                'tenant' => fn($q) => $q->select(['name', 'subdomain', 'id']),
            ]
        );

        return Response::success(
            data: StockMovementResource::collection($data),
        );
    }
}
