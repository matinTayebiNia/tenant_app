<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Modules\Core\Http\Middleware\HasUserAgent;
use Modules\Core\Support\Logger;
use Modules\Core\Helpers\ExceptionRender;
use Modules\Core\Helpers\GeneralExceptionHandler;
use Modules\Tenant\Support\Http\Middleware\DetectTenantScopeFromHeaderOrDomain;
use Modules\Tenant\Support\Http\Middleware\DetectTenantScopeFromSubDomain;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', HasUserAgent::class);
        $middleware->appendToGroup('api', DetectTenantScopeFromHeaderOrDomain::class);
        $middleware->trustProxies('*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions
            ->shouldRenderJsonWhen(fn(Request $req, Throwable $e) => true)
            ->render([ExceptionRender::class, 'notFound'])
            ->render([ExceptionRender::class, 'methodNotAllowed'])
            ->render([ExceptionRender::class, 'authentication'])
            ->render([ExceptionRender::class, 'validation'])
            ->render([ExceptionRender::class, 'throttleRequests'])
            ->render([ExceptionRender::class, 'httpException'])
            ->render([ExceptionRender::class, 'forbiddenException']);

        $exceptions->report(function (Exception $e) {
            Logger::logException($e);
        });

        // $exceptions->renderable(function (Throwable $e) {
        //     GeneralExceptionHandler::handle($e);
        // });
    })
    ->create();
