<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Support\Traits\HasDefaultActivityLogOption;
use Modules\Stock\Models\StockMovement;

class Tenant extends Model
{
    use HasDefaultActivityLogOption, HasDefaultSearchScope;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [
        'id',
        'created_at',
    ];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

}
