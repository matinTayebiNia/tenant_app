<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Services\LandingSearch\LandingSearchManager;

class ValidSearchTab
{

    public function handle(Request $request, Closure $next)
    {
        !in_array($request->route('tab'), LandingSearchManager::getSearchableTitles())
        &&
        abort(404);

        return $next($request);
    }
}
