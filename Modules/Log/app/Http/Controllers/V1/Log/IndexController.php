<?php

namespace Modules\Log\Http\Controllers\V1\Log;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Response;
use Modules\Core\DTOs\IndexDTO;
use Modules\Core\Exceptions\DBException;
use Modules\Log\Http\Requests\V1\IndexLogRequest;
use Modules\Log\Models\ActivityLog;
use Modules\Log\Transformers\V1\LogResource;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws DBException
     * @throws AuthorizationException
     */
    public function __invoke(IndexLogRequest $request)
    {

        $dto = IndexDTO::makeFromRequest(
            request: $request,
            searchFields: ['log_name', 'description', 'subject_id']
        );

        try {

            $activities = ActivityLog::with(['subject', 'causer'])->index($dto);

        } catch (QueryException $exception) {
            throw DBException::throw($exception);
        }

        return Response::success(
            data: LogResource::collection($activities),
        );

    }
}
