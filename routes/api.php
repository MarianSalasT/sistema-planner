<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\AuthController;

// Auth Routes (sin middleware)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('boards')->name('boards.')->group(function () {
        Route::get('my-boards', [BoardController::class, 'getMyBoards'])->name('getMyBoards');
        Route::post('{board}/add-member', [BoardController::class, 'addMember'])->name('addMember');
        Route::put('{board}/update-member-role', [BoardController::class, 'updateMemberRole'])->name('updateMemberRole');
        Route::post('{board}/remove-member', [BoardController::class, 'removeMember'])->name('removeMember');
        Route::get('deleted', [BoardController::class, 'getDeletedBoards'])->name('getDeletedBoards');
        Route::post('{board}/restore', [BoardController::class, 'restoreBoard'])->name('restoreBoard');
        Route::delete('{board}/force-delete', [BoardController::class, 'forceDeleteBoard'])->name('forceDeleteBoard');
        Route::get('my-deleted', [BoardController::class, 'getMyDeletedBoards'])->name('getMyDeletedBoards');
    });

    Route::apiResource('boards', BoardController::class);
    Route::apiResource('columns', ColumnController::class);
    Route::apiResource('cards', CardController::class);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('authenticate', [AuthController::class, 'authenticate']);
});
