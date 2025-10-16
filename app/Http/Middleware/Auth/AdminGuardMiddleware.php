<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminGuardMiddleware
{
    /**
     * Enforce authentication using the "admin" guard for web dashboards.
     *
     * Behavior:
     * - If the request expects JSON, return a 401 JSON response.
     * - Otherwise, redirect to the admin login route (fallbacks to /admin/login if route name missing).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Already authenticated with the admin guard → allow through
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Not authenticated → respond based on expected content type
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated (admin).',
            ], 401);
        }

        // Redirect web requests to admin login (use route name if defined)
        $loginUrl = route_exists('admin.login')
            ? route('admin.login')
            : url('/admin/login');

        return redirect()->guest($loginUrl);
    }
}

/**
 * Small helper to safely check if a route name is registered.
 * You can move this to a global helpers file if you prefer.
 */
if (! function_exists('route_exists')) {
    function route_exists(string $name): bool
    {
        try {
            return (bool) app('router')->has($name);
        } catch (\Throwable) {
            return false;
        }
    }
}
