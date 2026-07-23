<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ERPAuth
{
    public function handle(Request $request, Closure $next)
    {
        // ── Admin authenticated via Laravel Auth ──
        if (Auth::check()) {
            return $next($request);
        }

        // ── Allow logout route to bypass ──
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // ── Firm temp authenticated ──
        if (session('login_type') === 'firm' && session('firm_temp_authenticated')) {
            return redirect()->route('firm-selection');
        }

        // ── Firm authenticated via session ──
        if (session('login_type') === 'firm' && session('firm_id')) {
            return $next($request);
        }

        // ── Not authenticated → send to login ──
        return redirect()->guest(route('login'));
    }
}
