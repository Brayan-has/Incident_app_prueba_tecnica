<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {

    // auth routes
    Route::prefix('auth')->group(function () {

        Route::post('login', [AuthController::class, 'login'])
            ->middleware('guest');

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum');
    });

    // protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::apiResource('users', UserController::class);

        Route::get('incidents/status/{status_name}', [IncidentController::class,'incidentByStatus']);

        Route::get('incidents/expired', [IncidentController::class,'incidentsExpired']);

        Route::apiResource('incidents', IncidentController::class);
    });
});
