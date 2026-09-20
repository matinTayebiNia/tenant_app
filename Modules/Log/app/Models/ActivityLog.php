<?php

namespace Modules\Log\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Traits\HasAlias;
use Modules\Core\Traits\HasDefaultSearchScope;
use Modules\Log\Helper\LogHelper;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * @method static Carbon|null lastLogin()
 */
class ActivityLog extends SpatieActivity
{
    use HasAlias, HasDefaultSearchScope;

    protected $table = 'mod_log_activity_log';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    const SEARCH_ABLE_FIELDS = [];

    public function scopeLastLogin(Builder $query)
    {
        return $query->when(Auth::check(), function (Builder $query) {
            return $query->where('event', AuthActions::Login->value)
                ->where('causer_id', Auth::user()->id)
                ->latest()->first()?->created_at?->toDateTimeString();
        });
    }

    public function getUsername(): ?string
    {
        if ($this->causer) {
            return LogHelper::getCreator($this->causer, $this->causer->getCreatorFields());
        }

        return null;
    }

    public function getUserRole()
    {
        return $this->causer?->roles?->first()?->fa_name;
    }

}
