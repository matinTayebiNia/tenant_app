<?php

namespace Modules\Core\Exceptions;

use Illuminate\Database\QueryException;
use Modules\Core\Enums\ExceptionCode;

class DBException extends BaseException
{
    public static function throw(QueryException $queryException, array $extraParams = []): static
    {
        if (! app()->environment('production')) {
            $extraParams = [...$extraParams];
            $message = $queryException->getMessage();
        }

        return static::new(
            exceptionCode: ExceptionCode::DatabaseException,
            extraParams: $extraParams,
            message: @$message,
        );
    }
}
