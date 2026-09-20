<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanViewTelescope
{
    public function handle(Request $request, Closure $next): mixed
    {

        $username = config('telescope.TELESCOPE_USERNAME');
        $password = config('telescope.TELESCOPE_PASSWORD');

        if (
            ! $this->isSecureConnection($request) ||
            ! $this->isValidAuthorizationHeader($request) ||
            ! $this->isValidCredentials($request, $username, $password)
        ) {
            return response('Unauthorized.', Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }

    private function isSecureConnection($request)
    {
        return app()->environment('production') ? $request->secure() : true;
    }

    private function isValidAuthorizationHeader($request): bool
    {
        return $request->hasHeader('PHP_AUTH_USER') && $request->hasHeader('PHP_AUTH_PW');
    }

    private function isValidCredentials($request, $username, $password): bool
    {
        return $request->getUser() === $username &&
            $request->getPassword() === $password;

    }
}
