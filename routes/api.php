<?php

use App\Models\Gallery;
use App\Nova\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\TeamApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\WorkApiController;
use App\Http\Controllers\Api\ScoreApiController;
use App\Http\Controllers\Api\ContestApiController;
use App\Http\Controllers\Api\DiplomaApiController;
use App\Http\Controllers\Api\GalleryApiController;
use App\Http\Controllers\Api\SponsorsApiController;
use App\Http\Controllers\Api\TestimonialsApiController;

Route::middleware('api')->group(function () {
    Route::post('/login', [UserApiController::class, 'login']);
    Route::post('/logout', [UserApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [UserApiController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/register', [UserApiController::class, 'register']);
    Route::post('/contact', [ContactApiController::class, 'store']);
    Route::get('/gallery', [GalleryApiController::class, 'index']);
    Route::post('/verify-email', [UserApiController::class, 'verifyEmail']);
    Route::get('/works-front', [WorkApiController::class, 'getFrontWorks']);
    Route::get('/blogs', [BlogApiController::class, 'index']);
    Route::get('/blogs/{id}', [BlogApiController::class, 'show']);
    Route::get('/content/{link}/{page}', [TeamApiController::class, 'getContent'])->name('content.get');
    Route::get('/teams/{link}', [TeamApiController::class, 'show']);
    Route::post('/reset-password', [UserApiController::class, 'resetPassword'])->name('password.api.update');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user/profile', [UserApiController::class, 'viewUser']);
        Route::put('/user', [UserApiController::class, 'updateUser']);
        Route::delete('/user', [UserApiController::class, 'deleteUser']);
        Route::get('/user/artworks', [WorkApiController::class, 'getUserArtworks']);

        Route::get('/diplomas', [DiplomaApiController::class, 'index']);
        Route::get('/diplomas/{id}/download', [DiplomaApiController::class, 'download']);

        Route::get('/contests', [ContestApiController::class, 'index']);
        Route::get('/get-contest', [ContestApiController::class, 'getContest']);
        Route::get('/contests/{id}', [ContestApiController::class, 'show']);

        Route::get('/works', [WorkApiController::class, 'index']);
        Route::get('/works/{id}', [WorkApiController::class, 'show']);
        Route::post('/works', [WorkApiController::class, 'store']);
        Route::put('/works/{id}', [WorkApiController::class, 'update']);
        Route::delete('/works/{id}', [WorkApiController::class, 'destroy']);

        Route::get('/scores', [ScoreApiController::class, 'index']);
        Route::get('/scores/{id}', [ScoreApiController::class, 'show']);
        Route::post('/scores', [ScoreApiController::class, 'store']);
        Route::put('/scores/{id}/update', [ScoreApiController::class, 'update']);
        Route::put('/scores/{id}/finalize', [ScoreApiController::class, 'finalize']);
    });

    Route::get('/sponsors', [SponsorsApiController::class, 'index']);
    Route::post('forgot-password', [UserApiController::class, 'sendResetLinkEmail']);
    Route::get('/testimonials', [TestimonialsApiController::class, 'index']);
    Route::get('/test', function () {
        return response()->json(['message' => 'Hello World']);
    });
});
