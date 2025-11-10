<?php

use App\Http\Controllers\Api\AdminAppController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CardApiController;
use App\Http\Controllers\Api\MemberAppController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\TeamLeadAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectApiController::class, 'index']);
    Route::get('/{project}', [ProjectApiController::class, 'show']);
});

Route::prefix('cards')->group(function () {
    Route::get('/recent', [CardApiController::class, 'recent']);
    Route::get('/{card}', [CardApiController::class, 'show'])->whereNumber('card');
});

Route::prefix('app')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);

        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminAppController::class, 'dashboard']);
            Route::get('/monitoring', [AdminAppController::class, 'monitoring']);
            Route::get('/users', [AdminAppController::class, 'users']);
            Route::get('/reports', [AdminAppController::class, 'reports']);
        });

        Route::prefix('teamlead')->group(function () {
            Route::get('/dashboard', [TeamLeadAppController::class, 'dashboard']);
            Route::get('/solver', [TeamLeadAppController::class, 'solver']);
        });

        Route::prefix('member')->group(function () {
            Route::get('/dashboard', [MemberAppController::class, 'dashboard']);
            Route::get('/team', [MemberAppController::class, 'team']);
        });
    });
});
