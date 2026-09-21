<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Stock\Models\StockMovement;
use Modules\Tenant\Database\Factories\TenantFactory;

class Tenant extends Model
{
    use HasDefaultActivityLogOption, HasDefaultSearchScope, HasFactory;

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [
        'id',
        'created_at',
    ];

    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

}
