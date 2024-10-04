<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\WorkApiController;
use App\Http\Controllers\Api\ScoreApiController;
use App\Http\Controllers\Api\ContestApiController;
use App\Http\Controllers\Api\DiplomaApiController;



Route::middleware('api')->group(function () {

    // Login route
    Route::post('/login', [UserApiController::class, 'login']);

    // Logout route with Sanctum authentication
    Route::post('/logout', [UserApiController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/user', [UserApiController::class, 'user'])->middleware('auth:sanctum');

    // User routes
    Route::group(['middleware' => 'auth:sanctum'], function () {

        // User routes
        Route::get('/user', [UserApiController::class, 'viewUser']);
        Route::put('/user', [UserApiController::class, 'updateUser']);
        Route::delete('/user', [UserApiController::class, 'deleteUser']);
        Route::post('/user/notifications', [UserApiController::class, 'setNotifications']);

        // Diploma routes
        Route::get('/diplomas', [DiplomaApiController::class, 'index']);
        Route::get('/diplomas/{id}/download', [DiplomaApiController::class, 'download']);

        // Contest routes
        Route::get('/contests', [ContestApiController::class, 'index']);
        Route::get('/contests/{id}', [ContestApiController::class, 'show']);

        Route::get('/works', [WorkApiController::class, 'index']);
        Route::get('/works/{id}', [WorkApiController::class, 'show']);
        Route::post('/works', [WorkApiController::class, 'store']);
        Route::put('/works/{id}', [WorkApiController::class, 'update']);
        Route::delete('/works/{id}', [WorkApiController::class, 'destroy']);

        // Score routes
        Route::get('/scores', [ScoreApiController::class, 'index']);
        Route::get('/scores/{id}', [ScoreApiController::class, 'show']);
        Route::post('/scores', [ScoreApiController::class, 'store']);
        Route::put('/scores/{id}', [ScoreApiController::class, 'update']);
    });

    // Test route
    Route::get('/test', function () {
        return response()->json(['message' => 'Hello World']);
    });
});
