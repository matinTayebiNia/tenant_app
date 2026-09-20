<?php

namespace Modules\Core\Helpers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\HttpException as CustomHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionRender
{
    public static function notFound(NotFoundHttpException $e, Request $request): JsonResponse
    {
        if (($previous = $e->getPrevious()) instanceof ModelNotFoundException) {
            return self::modelNotFound($request, class_basename($previous->getModel()));
        }

        return self::httpNotFound($request);
    }

    public static function modelNotFound($request, $model): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::ModelNotFoundException,
            extraParams: ['model' => class_basename($model)]
        )->render($request);
    }

    public static function httpNotFound($request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::GenericNotFoundException,
            statusCode: 404
        )->render($request);
    }

    public static function methodNotAllowed(MethodNotAllowedHttpException $e, Request $request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::MethodNotAllowedHttpException
        )->render($request);
    }

    public static function authentication(AuthenticationException $e, Request $request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::AuthenticationException
        )->render($request);
    }

    public static function validation(ValidationException $e, Request $request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::ValidationException,
            extraParams: ['errors' => $e->errors()]
        )->render($request);
    }

    public static function throttleRequests(ThrottleRequestsException $e, Request $request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::GenericTooManyRequestsException,
        )->render($request);
    }

    public static function httpException(HttpException $e, Request $request): JsonResponse
    {
        return CustomHttpException::make(
            statusCode: $e->getStatusCode(),
            message: $e->getMessage(),
        )->render($request);
    }

    public static function forbiddenException(AccessDeniedHttpException $e, Request $request): JsonResponse
    {
        return BaseException::new(
            exceptionCode: ExceptionCode::GenericForbiddenException,
        )->render($request);
    }
}
