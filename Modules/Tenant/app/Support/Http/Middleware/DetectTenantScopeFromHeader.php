<?php

namespace Modules\Tenant\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Services\Repositories\TenantRepository;

class DetectTenantScopeFromHeader
{

    public function __construct(
        private  TenantRepository $tenantRepository,
    )
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('X-Tenant-Scope')) {
            abort(403, 'plz set tenant scope in header or in subdomain');
        }
        $subdomain = $request->header('X-Tenant-Scope');
        $tenant = $this->tenantRepository->findTenantBySubdomain($subdomain);

        if (!$tenant) {
            abort(403, 'plz set tenant scope in header or in subdomain');
        }

        TenantScopeConfig::setCurrent($tenant);

        return $next($request);

    }
}
