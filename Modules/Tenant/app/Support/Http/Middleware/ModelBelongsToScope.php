<?php

namespace Modules\Tenant\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Helper\TenantScopeConfig;

class ModelBelongsToScope
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $routeParam, bool $optionalParam = false)
    {

        $model = $request->route($routeParam);

        if ($optionalParam && is_null($model))
            return $next($request);

        if (!($model && ($scope = TenantScopeConfig::getCurrent()) && $model->belongsToSiteScope($scope)))
            abort(403, "tenant scope not matched");

        return $next($request);
    }
}
