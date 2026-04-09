<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\AuthCheckMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            ForceJsonResponse::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Alias register
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'admin_auth' => AdminAuthMiddleware::class,
            'auth_check' => AuthCheckMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ============================================
        // API Route Not Found (404)
        // ============================================
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'API route not found.',
                    'path' => $request->path(),
                ], 404);
            }
        });

        // ============================================
        // Wrong HTTP Method (405)
        // ============================================
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $allowedMethods = $e->getHeaders()['Allow'] ?? '';

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request method.',
                    'allowed_methods' => $allowedMethods,
                    'path' => $request->path(),
                ], 405);
            }
        });

        // ============================================
        // Model Not Found (404)
        // ============================================
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

    })
    ->create();
