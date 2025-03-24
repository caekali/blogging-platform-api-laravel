<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Notifications\MailTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\ApiResponse;

Route::get('/profile', function (Request $request) {
    return ApiResponse::success($request->user());
})->middleware('auth:sanctum');


Route::post('/notify', function (Request $request) {
    return $request->user()->notify(new MailTest());
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->prefix('/auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/{userId}/posts', [PostController::class, 'index']);
    Route::get('/users/{userId}/posts/{postId}', [PostController::class, 'show']);
    Route::post('/users/{userId}/posts', [PostController::class, 'store']);
    Route::put('/users/{userId}/posts/{postId}',[PostController::class, 'update']);
    Route::delete('/users/{userId}/posts/{postId}', [PostController::class, 'destroy']);


    Route::get('/posts/{postId}/comments', [CommentController::class, 'getAllByPost']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'makeComment']);
    Route::put('/posts/{postId}/comments/{commentId}',[PostController::class, 'update']);
    Route::delete('/comments/{commentId}', [CommentController::class, 'destroy']);
});
