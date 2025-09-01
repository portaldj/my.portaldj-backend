<?php

use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [UserProfileController::class, 'me'])->name('profile.me');
        Route::post('/profile/{user}', [UserProfileController::class, 'update'])->name('profile.update');

        Route::apiResource('posts', PostController::class)->except(['index', 'show']);

        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
        Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::put('/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
        Route::put('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

        Route::apiResource('schedules', ScheduleController::class);

        Route::middleware('role:administrator')->prefix('admin')->name('admin.')->group(function () {

            Route::apiResource('users', UserController::class);

            Route::apiResource('skills', SkillController::class);
            Route::apiResource('music-genres', MusicGenreController::class);
            Route::apiResource('profile-types', ProfileTypeController::class);
            Route::apiResource('clubs', ClubController::class);
        });
    });
});
