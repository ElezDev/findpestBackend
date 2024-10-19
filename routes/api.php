<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetImageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);


Route::middleware('auth:api')->group(function () {
    Route::get('pets', [PetController::class, 'index']);
    Route::post('pets', [PetController::class, 'store']);
    Route::get('pets/{pet}', [PetController::class, 'show']);
    Route::put('pets/{pet}', [PetController::class, 'update']);
    Route::delete('pets/{pet}', [PetController::class, 'destroy']);
});

Route::apiResource('images_pets', PetImageController::class)->only(['index', 'show', 'store', 'destroy']);
Route::post('images_pets/{id}', [PetImageController::class, 'update']);

Route::middleware('auth:api')->get('user_data', [AuthController::class, 'getAuthenticatedUser']);
