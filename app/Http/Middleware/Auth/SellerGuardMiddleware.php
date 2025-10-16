<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerGuardMiddleware
{
    /**
     * Enforce authentication using the "seller" guard for seller dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('seller')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated (seller).',
            ], 401);
        }

        $loginUrl = route_exists('seller.login')
            ? route('seller.login')
            : url('/seller/login');

        return redirect()->guest($loginUrl);
    }
}
