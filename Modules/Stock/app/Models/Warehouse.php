<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Stock\Database\Factories\StockLevelFactory;
use Modules\Stock\Database\Factories\WarehouseFactory;
use Modules\Tenant\Support\Traits\HasTenantScope;
use Modules\Tenant\ValueObject\TenantScopeSetting;

class Warehouse extends Model
{

    use HasDefaultActivityLogOption, HasDefaultSearchScope, HasTenantScope, HasFactory;

    const UPDATED_AT = null;

    protected $guarded = [
        'id',
        'created_at',
    ];

    protected static function newFactory(): WarehouseFactory
    {
        return WarehouseFactory::new();
    }

    public static function setupTenantScope(): TenantScopeSetting
    {
        return TenantScopeSetting::singleScope();
    }
}
