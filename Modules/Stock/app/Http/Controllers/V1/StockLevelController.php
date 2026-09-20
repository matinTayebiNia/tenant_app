<?php

namespace Modules\Stock\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Core\DTOs\IndexDTO;
use Modules\Stock\Dto\V1\StockLevelFilterDto;
use Modules\Stock\Http\Requests\V1\StockLevelFilterRequest;
use Modules\Stock\Services\StockLevelService;
use Modules\Stock\Transformers\V1\StockLevelResource;

class StockLevelController extends Controller
{

    public function __construct
    (
        private StockLevelService $service,
    )
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(StockLevelFilterRequest $request)
    {

        $data = $this->service->index(
            IndexDTO::makeFromRequest($request),
            StockLevelFilterDto::makeFromRequest($request),
            [
                'product' => fn($q) => $q->select(['id', 'name', 'sku']),
                'warehouse' => fn($q) => $q->select(['id', 'name']),
                'tenant' => fn($q) => $q->select(['id', 'name', 'subdomain']),
            ]
        );

        return Response::success(
            data: StockLevelResource::collection($data),
        );

    }
}
