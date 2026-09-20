<?php

namespace Modules\Core\Support;

use Exception;
use Illuminate\Support\Facades\Log;

class Logger
{
    public static function logException(Exception $exception): void
    {
        Log::info(ExceptionFormat::log($exception));
    }
}
