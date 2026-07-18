<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // ── Firm session: bypass all permission checks ──
        // Firm owners have full access to their own data.
        if (session('login_type') === 'firm' && session('firm_id')) {
            return $next($request);
        }

        // ── Admin must be authenticated ──
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super Admin / Admin bypass all checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Regular user — check specific permission
        if (! $user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access Denied. You do not have permission to perform this action.',
                ], 403);
            }

            abort(403, 'Access Denied. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
