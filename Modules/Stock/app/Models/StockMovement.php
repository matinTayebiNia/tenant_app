<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Product\Models\Product;
use Modules\Stock\Enums\StockType;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Support\Traits\HasTenantScope;
use Modules\Tenant\ValueObject\TenantScopeSetting;

class StockMovement extends Model
{

    use HasDefaultSearchScope, HasDefaultActivityLogOption, HasTenantScope;


    const  SEARCH_FIELDS = ['reference'];
    const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id', 'created_at'];


    protected $casts = [
        'type' => StockType::class,
        'created_at' => 'datetime',
        'meta' => 'array',
    ];

    public static function setupTenantScope(): TenantScopeSetting
    {
        return TenantScopeSetting::singleScope();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
