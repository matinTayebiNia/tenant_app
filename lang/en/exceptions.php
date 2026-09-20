<?php

use Modules\Core\Enums\ExceptionCode;

return [
    'default' => [
        'title' => 'unknown error',
        'message' => 'an error occurred',
        'description' => 'no more descriptions were provided',
    ],
    ExceptionCode::MethodNotAllowedHttpException->value => [
        'message' => 'this http method is not supported for this route',
        'description' => 'there might be an issue with using wrong http method for this route',
    ],
    ExceptionCode::ValidationException->value => [
        'message' => 'validation error occurred',
        'description' => 'there was something wrong with one or more fields of the request',
    ],
    ExceptionCode::AuthenticationException->value => [
        'message' => 'problem in authentication occurred',
        'description' => 'there was a problem in authentication',
    ],
    ExceptionCode::ModelNotFoundException->value => [
        'message' => 'the model was not found',
        'description' => 'the requested model (probably as a url param) was not found',
    ],
    ExceptionCode::DatabaseException->value => [
        'message' => 'Database had some errors',
        'description' => 'Database might have not worked correctly, please try again',
    ],
    ExceptionCode::ExternalApiException->value => [
        'message' => 'external api has returned an error',
        'description' => 'check external api configs on both ends and try again',
    ],

    // generics
    ExceptionCode::GenericNotFoundException->value => [
        'message' => 'this route does not exist',
        'description' => 'check your route and try again',
    ],
    ExceptionCode::GenericTooManyRequestsException->value => [
        'message' => 'too many requests made in a short amount of time',
        'description' => 'try again later when resource is not being limited',
    ],
    ExceptionCode::GenericBadRequestException->value => [
        'message' => 'sent request cannot be processed',
        'description' => 'check sent data and params and try again',
    ],
    ExceptionCode::GenericForbiddenException->value => [
        'message' => 'you are not allowed to access this resource',
        'description' => 'you are logged in but do not have access to this request',
    ],
    ExceptionCode::GenericInternalServerError->value => [
        'message' => 'an error occurred on the server side',
        'description' => 'contact server admins for more info',
    ],
    ExceptionCode::GenericUnauthorizedException->value => [
        'message' => 'you are not logged in',
        'description' => 'log in and try accessing this resource again',
    ],

];
