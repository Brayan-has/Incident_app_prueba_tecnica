<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            SetTeamUrlDefaults::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            
            $response = match(true) {
                $e instanceof \App\Exceptions\CrudException => $e->render($request),
                $e instanceof \Illuminate\Validation\ValidationException => response()->json([
                    "message" => $e->errors()
                ], 422),
                $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException => response()->json([
                    "message" => "Method not allowed",
                    "method used" => $request->method()
                ], 405),
                $e instanceof \Illuminate\Auth\AuthenticationException => response()->json([
    "message" => "Unauthenticated."
], 401),
                $e instanceof \BadMethodCallException => response()->json([
                    "message" => "This format is not allowed or is not working",
                    "error" => $e->getMessage()
                ], 500),
                $e instanceof \Illuminate\Database\UniqueConstraintViolationException => response()->json([
                    "message" => "The data for this resource already exist"
                ], 400),
                $e instanceof \Illuminate\Database\QueryException => response()->json([
                    "message" => "incorrect format",
                    "error" => $e->errorInfo
                ], 400),
                $e instanceof \App\Exceptions\RoleDoesNotExist => response()->json([ 
                    "error" => "something was wrong",
                    "message" => $e->getMessage()
                ], 500),
                $e instanceof \App\Exceptions\ForbiddenException => response()->json([
                    "message" => "You don't have permission to access this resource"
                ], 403),
                default => null, 
            };
            
            if ($response) {
                return $response;
            }
            
            
        });
    })->create();
