<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

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

        // roles endpoints
        Route::get('roles', [RoleController::class, 'getAllRoles']);
        Route::post('roles/assign/{user_id}', [RoleController::class, 'assignRoleToUser']);
        Route::get('roles/user', [RoleController::class, 'getRoleByUser']);

        Route::get('permissions', [RoleController::class, 'getAllPermissions']);


    });
});
