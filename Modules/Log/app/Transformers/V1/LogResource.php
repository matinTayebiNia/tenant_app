<?php

namespace Modules\Log\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Log\Models\ActivityLog;

/**
 * @property-read ActivityLog $resource
 */
class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $singularName = last(explode('.', $this->resource->log_name));

        $id = $this->resource->subject_id ?? $this->resource->causer_id;

        $eventSubject = __("Log::names.singular.$singularName", ['id' => $id]);

        $logName = $this->resource->log_name;

        return [
            'id' => $this->resource->getKey(),
            'event_type' => __($logName),
            'event_subject' => $eventSubject,
            'type' => __('Log::validation.events.'.$this->resource->event),
            'user' => $this->resource->getUsername(),
            'role' => $this->resource->getUserRole(),
            'created_at' => $this->resource->created_at?->toDateTimeString(),
        ];

    }
}
