<?php

namespace Modules\Stock\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Enums\CrudOperation;
use Modules\Stock\Dto\V1\StockMovementDto;
use Modules\Stock\Http\Requests\V1\StockMovementRequest;
use Modules\Stock\Services\StockMovementService;

class StockMovementController extends Controller
{

    public function __construct(
        private StockMovementService $service,
    )
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(StockMovementRequest $request)
    {

        $this->service->create(
            StockMovementDto::make($request->validated()),
        );

        return Response::crudSuccess(
            model: "انبار",
            crudOperation: CrudOperation::STORE
        );

    }

}
