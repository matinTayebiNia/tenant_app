<?php

namespace Modules\Tenant\Services\Repositories;

use Modules\Tenant\Models\Tenant;

class TenantRepository
{

    public function findTenantBySubdomain(string $subdomain): ?Tenant
    {
        return  Tenant::query()->where('subdomain', $subdomain)->first();
    }

}
