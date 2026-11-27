<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Caval OS: the custom admin, served from the same app and database.
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix(config('adminos.path', 'adminos'))
                ->name('os.')
                ->group(base_path('routes/adminos.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'os.admin'  => \App\Http\Middleware\AdminOs\EnsureOsAdmin::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'   => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API errors always use { "error": { code, message, fields } } (backend spec 6.2).
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(function (\Throwable $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return match (true) {
                $e instanceof \Illuminate\Validation\ValidationException => \App\Support\ApiError::make(
                    'validation_failed', 'Some details need checking.', 422, $e->errors()),
                $e instanceof \Illuminate\Auth\AuthenticationException => \App\Support\ApiError::make(
                    'unauthenticated', 'Please sign in again.', 401),
                $e instanceof \Laravel\Sanctum\Exceptions\MissingAbilityException,
                $e instanceof \Illuminate\Auth\Access\AuthorizationException => \App\Support\ApiError::make(
                    'forbidden', 'You can\'t do that.', 403),
                $e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException => \App\Support\ApiError::make(
                    'too_many_requests', 'Too many tries. Please wait a moment and try again.', 429)
                    ->withHeaders($e->getHeaders()),
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
                $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => \App\Support\ApiError::make(
                    'not_found', 'Not found.', 404),
                $e instanceof \Illuminate\Http\Exceptions\HttpResponseException => $e->getResponse(),
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface => \App\Support\ApiError::make(
                    'bad_request', $e->getMessage() ?: 'Bad request.', $e->getStatusCode()),
                default => config('app.debug') ? null : \App\Support\ApiError::make(
                    'server_error', 'Something went wrong on our side. Please try again.', 500),
            };
        });
    })->create();
