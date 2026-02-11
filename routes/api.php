<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::apiResource('posts', PostController::class)->only(['index', 'show', 'store', 'update']);
Route::post('posts/{post}/comments', [CommentController::class, 'store']);
