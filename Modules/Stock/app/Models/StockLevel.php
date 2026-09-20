<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Product\Models\Product;
use Modules\Stock\Database\Factories\StockLevelFactory;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Support\Traits\HasTenantScope;
use Modules\Tenant\ValueObject\TenantScopeSetting;

class StockLevel extends Model
{

    use HasDefaultActivityLogOption, HasTenantScope, HasDefaultSearchScope;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [
        'id',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
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
