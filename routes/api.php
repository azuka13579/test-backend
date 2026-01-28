<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController as ApiCommentController;
use App\Http\Controllers\Api\PostController as ApiPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\CommentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/students', [StudentController::class, 'index']);
// Route::get('/students/{id}', [StudentController::class, 'show']);
// Route::post('/students', [StudentController::class, 'store']);
// Route::patch('/students/{id}', [StudentController::class, 'store']);
// Route::put('/students{id}', [StudentController::class, 'store']);
// Route::delete('/students/{id}', [StudentController::class, 'destroy']);


// Public Route
Route::post('/login', [AuthController::class, 'login']);
Route::get('/anjay', [AuthController::class, 'test']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('students', StudentController::class);
    Route::get('/posts/user', [ApiPostController::class, 'userPost']);
    Route::apiResource('posts', ApiPostController::class);

    Route::apiResource('comments', ApiCommentController::class);
});

