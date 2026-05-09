<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/v1')->group(function () {
    # users endpoints
    Route::apiResource('users', UserController::class);
    # incidents endpoints
    Route::apiResource('incidents', IncidentController::class);
});