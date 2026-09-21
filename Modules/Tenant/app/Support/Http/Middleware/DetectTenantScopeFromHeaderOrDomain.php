<?php

namespace Modules\Tenant\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Helper\TenantScopeConfig;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Services\Repositories\TenantRepository;

class DetectTenantScopeFromHeaderOrDomain
{

    public function __construct(
        private TenantRepository $tenantRepository,
    )
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->detectFromSubdomain($request);

        TenantScopeConfig::setCurrent($tenant);

        return $next($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function detectFromHeader(Request $request)
    {
        if (!$request->hasHeader('X-Tenant-Scope')) {
            abort(403, 'plz set tenant scope in header or in subdomain');
        }
        $subdomain = $request->header('X-Tenant-Scope');
        $tenant = $this->tenantRepository->findTenantBySubdomain($subdomain);

        if (!$tenant) {
            abort(403, 'plz set tenant scope in header or in subdomain');
        }

        return $tenant;
    }

    private function detectFromSubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        if (!$subdomain) {
            abort(403, 'plz set tenant scope in header or in subdomain');
        }
        $tenant = $this->tenantRepository->findTenantBySubdomain($subdomain);

        if (!$tenant) {
            return $this->detectFromHeader($request);
        }
        return $tenant;
    }
}
