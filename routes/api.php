<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\PoolController;
use App\Http\Controllers\Api\AcademyController;
use App\Http\Controllers\Api\EquipmentController;

Route::get('equipment', [EquipmentController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // Pool
    Route::get('/pool', [PoolController::class, 'index']);
    Route::get('/pool/{song}/download', [PoolController::class, 'download']);

    // Academy
    Route::get('/academy', [AcademyController::class, 'index']);
    Route::get('/academy/{course}', [AcademyController::class, 'show']);
    Route::post('/academy/exams/{exam}/submit', [AcademyController::class, 'submitExam']);

    // Feed
    Route::get('/feed', [FeedController::class, 'index']);
    Route::post('/feed', [FeedController::class, 'store']);
    Route::post('/feed/{post}/comment', [FeedController::class, 'comment']);
});