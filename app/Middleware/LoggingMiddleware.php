<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Pre-processing
        $request->attributes->set('logged_at', microtime(true));

        $response = $next($request);

        // Post-processing
        if (method_exists($response, 'header')) {
            $response->header('X-Laralite-Framework', 'Active');
        }

        return $response;
    }
}
