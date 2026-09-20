<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Stock\Models\StockMovement;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Support\Traits\HasTenantScope;
use Modules\Tenant\ValueObject\TenantScopeSetting;

class Product extends Model
{

    use HasTenantScope, HasDefaultSearchScope, HasDefaultActivityLogOption;

    protected $guarded = [
        'id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];


    const SEARCH_FIELDS = ['sku', 'name', 'description'];

    public static function setupTenantScope(): TenantScopeSetting
    {
        return TenantScopeSetting::singleScope();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
