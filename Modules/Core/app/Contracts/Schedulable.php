<?php

namespace Modules\Core\Contracts;

interface Schedulable
{
    public function isScheduled(): bool;

    public function isPublishable(): bool;
}
