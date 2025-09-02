<?php

use App\Http\Controllers\Api\V1\Admin\CityController;
use App\Http\Controllers\Api\V1\Admin\ClubController;
use App\Http\Controllers\Api\V1\Admin\CountryController;
use App\Http\Controllers\Api\V1\Admin\EquipmentBrandController;
use App\Http\Controllers\Api\V1\Admin\EquipmentModelController;
use App\Http\Controllers\Api\V1\Admin\EquipmentTypeController;
use App\Http\Controllers\Api\V1\Admin\MusicGenreController;
use App\Http\Controllers\Api\V1\Admin\ProfileTypeController;
use App\Http\Controllers\Api\V1\Admin\SkillController;
use App\Http\Controllers\Api\V1\Admin\SocialNetworkController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\UserProfileController;
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

        Route::middleware('role:'.\App\Models\Role::ROLE_ADMIN['name'])->prefix('admin')->name('admin.')->group(function () {

            Route::apiResource('users', UserController::class);

            Route::apiResource('skills', SkillController::class);
            Route::apiResource('music-genres', MusicGenreController::class);
            Route::apiResource('profile-types', ProfileTypeController::class);
            Route::apiResource('clubs', ClubController::class);

            Route::apiResource('cities', CityController::class);
            Route::apiResource('countries', CountryController::class);

            Route::apiResource('equipment-brands', EquipmentBrandController::class);
            Route::apiResource('equipment-models', EquipmentModelController::class);
            Route::apiResource('equipment-types', EquipmentTypeController::class);

            Route::apiResource('social-networks', SocialNetworkController::class);
        });
    });
});
