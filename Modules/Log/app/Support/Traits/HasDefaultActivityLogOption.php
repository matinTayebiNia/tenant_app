<?php

namespace Modules\Log\Support\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasDefaultActivityLogOption
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {

        $name = 'Log::names.'.class_basename(static::class);

        return LogOptions::defaults()
            ->useLogName($name)
            ->logUnguarded()
            ->logFillable()
            ->setDescriptionForEvent(function (string $eventName) use ($name) {
                $name = __($name);

                return match ($eventName) {
                    'created' => __('Log::events.created', ['model' => $name]),
                    'updated' => __('Log::events.updated', ['model' => $name]),
                    'deleted' => __('Log::events.deleted', ['model' => $name]),
                };
            })
            ->logOnlyDirty();

    }

    public function getCreatorFields(): array
    {
        return ['firstname', 'lastname'];
    }
}
