<?php

namespace App\Http\Middleware;

use App\Helpers\Error;
use App\Helpers\Respondable;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedMiddleware
{
    use Respondable;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $check = AuthService::check();

        if ($check instanceof Error) {
            return $this->respondError($check);
        }

        return $next($request);
    }
}
