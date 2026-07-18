<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If logged in as a firm, block access to admin modules
        if (session('login_type') === 'firm' || session()->has('firm_id')) {
            abort(403, 'Access Denied. This section is restricted to Administrators only.');
        }

        return $next($request);
    }
}
