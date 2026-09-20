<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Helper\CategoryRelation;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;

class RestrictedRelation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $routeParam, string $msg = '')
    {
        is_object($model = $request->route($routeParam))
        &&
        $model?->hasRestrictedRelations()
        &&
        throw BaseException::new(
            exceptionCode: ExceptionCode::GenericForbiddenException,
            message: $msg ?: $model->restrictedErrMsg()
        );

        return $next($request);
    }
}
