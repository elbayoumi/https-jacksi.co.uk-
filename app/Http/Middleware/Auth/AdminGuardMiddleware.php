<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminGuardMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated (admin).',
            ], 401);
        }

        $loginUrl = Route::has('admin.login')
            ? route('admin.login')
            : url('/admin/login');

        return redirect()->guest($loginUrl);
    }
}
