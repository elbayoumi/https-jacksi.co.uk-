<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Throwable;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        // Register multiple web route files. Order matters.
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/web/admin.php',
            __DIR__ . '/../routes/web/seller.php',
        ],

        // Console commands route file (Artisan scheduled/closure commands).
        commands: __DIR__ . '/../routes/console.php',

        // Lightweight health-check endpoint. Returns 200 when the app boots.
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * Register global and route middleware here.
         *
         * - Use $middleware->append(...) to push a global middleware to the end of the stack.
         * - Use $middleware->prepend(...) to push a global middleware to the beginning.
         * - Use $middleware->alias([...]) to define short names for route middleware.
         */

        // Example: append a global middleware (executed on every request).
        // $middleware->append(\App\Http\Middleware\TrustProxies::class);

        // Example: prepend a global middleware (executed before everything else).
        // $middleware->prepend(\App\Http\Middleware\ForceHttps::class);

        // Route middleware aliases (use short names inside Route::middleware([...])).
        $middleware->alias([
            // Auth guards (session-based dashboards)
            'auth.admin'  => \App\Http\Middleware\Auth\AdminGuardMiddleware::class,   // expects guard: admin
            'auth.seller' => \App\Http\Middleware\Auth\SellerGuardMiddleware::class, // expects guard: seller

            // Example: role/ability gates
            // 'can.manage.invoices' => \App\Http\Middleware\Abilities\ManageInvoices::class,

            // Example: locale switcher or other cross-cutting concerns
            // 'locale' => \App\Http\Middleware\SetLocaleFromRequest::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * Centralized exception handling.
         * Add targeted report/ignore logic and clean API JSON responses.
         */

        // Example: Custom reporting hook (e.g., Sentry/Bugsnag or structured logging).
        // $exceptions->report(function (Throwable $e) {
        //     if ($e instanceof \DomainException) {
        //         // report to external tracker or add context
        //     }
        // });

        // Render a consistent JSON for API consumers when they explicitly accept JSON.
        $exceptions->render(function (Throwable $e) {
            $request = request();

            // Honor JSON requests (Accept: application/json or AJAX with expectsJson()).
            if ($request->expectsJson()) {
                $status = 500;

                // Minimal, defensive mapping — extend as needed.
                // Keep detailed messages out of production unless you gate by APP_DEBUG.
                if (method_exists($e, 'getStatusCode')) {
                    /** @var \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e */
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'status'  => false,
                    'message' => app()->hasDebugModeEnabled()
                        ? $e->getMessage()
                        : 'Unexpected error. Please try again or contact support.',
                ], $status);
            }

            // Fallback to Laravel’s default renderer for web requests (HTML).
            return null; // returning null delegates to the framework
        });
    })

    ->create();
