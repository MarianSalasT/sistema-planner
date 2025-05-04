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

    Route::get('boards/my-boards', [BoardController::class, 'getMyBoards'])->name('boards.getMyBoards');
    Route::post('boards/{board}/add-member', [BoardController::class, 'addMember'])->name('boards.addMember');
    Route::post('boards/{board}/remove-member', [BoardController::class, 'removeMember'])->name('boards.removeMember');
    Route::apiResource('boards', BoardController::class);

    Route::apiResource('columns', ColumnController::class);

    Route::apiResource('cards', CardController::class);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('authenticate', [AuthController::class, 'authenticate']);
});
