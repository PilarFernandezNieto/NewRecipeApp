<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DifficultyController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});

// Rutas públicas
Route::get('difficulties',            [DifficultyController::class, 'index']);
Route::get('recipes',                 [RecipeController::class, 'index']);
Route::get('recipes/{recipe}',        [RecipeController::class, 'show']);
Route::get('categories',              [CategoryController::class, 'index']);
Route::get('categories/{category}',   [CategoryController::class, 'show']);
Route::get('ingredients',             [IngredientController::class, 'index']);
Route::get('ingredients/{ingredient}',[IngredientController::class, 'show']);

// Rutas solo admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('recipes',            [RecipeController::class, 'store']);
    Route::post('recipes/{recipe}',   [RecipeController::class, 'update']);
    Route::delete('recipes/{recipe}', [RecipeController::class, 'destroy']);

    Route::post('categories',              [CategoryController::class, 'store']);
    Route::post('categories/{category}',   [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('ingredients',               [IngredientController::class, 'store']);
    Route::put('ingredients/{ingredient}',   [IngredientController::class, 'update']);
    Route::delete('ingredients/{ingredient}',[IngredientController::class, 'destroy']);
});
