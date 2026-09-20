<?php

namespace Modules\Core\Helpers;

use Illuminate\Database\QueryException;
use Modules\Core\Exceptions\DBException;
use Throwable;

class GeneralExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        if ($e instanceof QueryException) {
            throw DBException::throw($e);
        }

        throw $e;
    }
}
