<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     * In Non-SaaS mode, all authenticated requests belong to the single organization.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
