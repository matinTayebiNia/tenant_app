<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Tenant\Support\Traits\HasTenantScope;
use Modules\Tenant\ValueObject\TenantScopeSetting;

class Warehouse extends Model
{

    use HasDefaultActivityLogOption, HasDefaultSearchScope, HasTenantScope;

    protected $guarded = [
        'id',
        'created_at',
    ];

    public static function setupTenantScope(): TenantScopeSetting
    {
        return TenantScopeSetting::singleScope();
    }
}
