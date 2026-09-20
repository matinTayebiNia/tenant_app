<?php

namespace Modules\Core\Enums;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

enum ExceptionCode: int
{
    // rare status codes
    case MethodNotAllowedHttpException = 10001;
    case ValidationException = 10002;
    case GenericTooManyRequestsException = 10003;

    // 401
    case AuthenticationException = 1;
    case GenericUnauthorizedException = 2;

    // 404
    case GenericNotFoundException = 1000;
    case ModelNotFoundException = 1001;

    // 500
    case DatabaseException = 2000;
    case ExternalApiException = 2001;
    case GenericInternalServerError = 2003;
    case TempUrlCreationFailed = 2004;
    case TempUrlUpdateFailed = 2005;
    case InvalidArgs = 2006;
    case InvalidMethod = 2007;

    // 400
    case GenericBadRequestException = 3000;
    case HttpException = 3001;

    case FileNotFound = 3002;
    case DirectoryNotFound = 3003;
    case DirectoryExists = 3004;
    case FileExists = 3005;
    case WrongPasswordForZipFile = 3006;
    case ZipFileCouldNotBeOpened = 3007;
    case NoPasswordProvidedForZipFile = 3008;
    case PrivateFileLinkIsNotAvailable = 3009;
    case NotAvailableForPublicDisks = 3010;
    case TempUrlAlreadyExists = 3011;
    case TempUrlLinkExpired = 3012;

    // 403
    case GenericForbiddenException = 4000;

    public function getDescription(): string
    {
        $key = "Core::exceptions.$this->value.description";
        $translation = __($key);

        if ($translation === $key) {
            return __('Core::exceptions.default.description');
        }

        return $translation;
    }

    public function getMessage(): string
    {
        $key = "Core::exceptions.$this->value.message";
        $translation = __($key);

        if ($translation === $key) {
            return __('Core::exceptions.default.message');
        }

        return $translation;
    }

    public function getTitle(int $statusCode): string
    {
        $key = "http-statuses.$statusCode";
        $translation = __($key);

        if ($translation === $key) {
            return __('Core::exceptions.default.title');
        }

        return $translation;
    }

    public function getCodeStatus(): int
    {
        $value = $this->value;

        return match (true) {
            $value === self::GenericTooManyRequestsException->value => ResponseAlias::HTTP_TOO_MANY_REQUESTS,
            $value === self::ValidationException->value => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
            $value === self::MethodNotAllowedHttpException->value => ResponseAlias::HTTP_METHOD_NOT_ALLOWED,
            $value >= 4000 => ResponseAlias::HTTP_FORBIDDEN,
            $value >= 3000 => ResponseAlias::HTTP_BAD_REQUEST,
            $value >= 2000 => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
            $value >= 1000 => ResponseAlias::HTTP_NOT_FOUND,
            $value >= 1 => ResponseAlias::HTTP_UNAUTHORIZED
        };
    }
}
