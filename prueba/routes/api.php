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
        Route::post('users/{id}/restore', [UserController::class, 'restore']);
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);

        Route::get('incidents/expired', [IncidentController::class,'incidentsExpired']);
        Route::get('incidents/status/{status_name}', [IncidentController::class,'incidentByStatus']);

        Route::apiResource('incidents', IncidentController::class);
        Route::post('incidents/{id}/restore', [IncidentController::class, 'restore']);
        Route::delete('incidents/{id}/force-delete', [IncidentController::class, 'forceDelete']);

        // roles endpoints
        Route::get('roles', [RoleController::class, 'getAllRoles']);
        Route::post('roles/assign/{user_id}', [RoleController::class, 'assignRoleToUser']);
        Route::get('roles/me', [RoleController::class, 'getCurrentUserRole']);
        Route::get('roles/{user_id}', [RoleController::class, 'getRoleByUser']);

        // permissions endpoints
        Route::get('permissions', [RoleController::class, 'getAllPermissions']);
        Route::post('permissions/assign/{user_id}', [RoleController::class, 'assignPermission']);


    });
});
