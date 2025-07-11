<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Closure;
use App\Services\AuthService;
use App\Helpers\Respondable;
use App\Helpers\Error;

// 👉 This middleware is for access control via permissions
class ProtectedRouteMiddleware
{
    use Respondable;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        [$controller, $method] = explode('@', Route::currentRouteAction());

        if (defined("$controller::PERMISSIONS") && array_key_exists($method, $controller::PERMISSIONS)) {
            $user = AuthService::me();

            if (!$user) {
                return $this->respondError(new Error('You are not authorized to access this resource', 401));
            }

            $permissions = $controller::PERMISSIONS[$method];

            /**
             * @var \App\Models\User $user
             */
            if (
                !$user->hasAnyPermission($permissions) && !$user->isAdmin() && !$user->isOwner()
            ) {
                return $this->respondError(new Error('You are not authorized to access this resource', 403));
            }
        }

        return $next($request);
    }
}
