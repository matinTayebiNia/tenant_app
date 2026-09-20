<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;

class HasUserAgent
{
    /**
     * Handle an incoming request.
     *
     * @throws BaseException
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->headers->has('User-Agent')) {
            return $next($request);
        }

        throw BaseException::new(
            ExceptionCode::GenericBadRequestException,
            message: __('Core::responses.bad_request'),
        );

    }
}
