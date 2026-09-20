<?php

namespace Modules\Core\Exceptions;

use Modules\Core\Enums\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;

class HttpException extends BaseException
{
    public static function make(
        int $statusCode,
        array $extraParams = [],
        ?string $message = null,
        ?string $description = null,
    ): static {
        $exceptionCode = match ($statusCode) {
            Response::HTTP_BAD_REQUEST => ExceptionCode::GenericBadRequestException,
            Response::HTTP_UNAUTHORIZED => ExceptionCode::GenericUnauthorizedException,
            Response::HTTP_FORBIDDEN => ExceptionCode::GenericForbiddenException,
            Response::HTTP_INTERNAL_SERVER_ERROR => ExceptionCode::GenericInternalServerError,
            Response::HTTP_TOO_MANY_REQUESTS => ExceptionCode::GenericTooManyRequestsException,
            Response::HTTP_NOT_FOUND => ExceptionCode::GenericNotFoundException,
            default => ExceptionCode::HttpException
        };

        $message = $message === '' ? null : $message;

        return static::new($exceptionCode, $extraParams, $message, $description);
    }
}
