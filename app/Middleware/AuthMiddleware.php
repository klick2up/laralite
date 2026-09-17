<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthMiddleware
{
    /**
     * Handle an incoming request for route group protection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Simple token check for demonstration
        if ($request->header('Authorization') === 'UnauthorizedToken') {
            return new JsonResponse(['error' => 'Unauthorized access'], 401);
        }

        return $next($request);
    }
}
