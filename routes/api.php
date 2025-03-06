<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('/auth/google',[AuthController::class,'redirectToGoogle']);
// Route::get('/auth/google/callback',[AuthController::class,'handleGoogleCallback']);
Route::post('/auth/google', [AuthController::class, 'handleGoogleAuth']);


/**
 * Route for Category
 */
Route::post('/categories', [CategoryController::class, 'storecategory']);
Route::post('/sousCategory', [CategoryController::class, 'createsouscategory']);
Route::get('/affichageCategory', [CategoryController::class, 'affichagecategory']);


//Route for announcements
Route::middleware('auth:api')->resource('ads', AdController::class)->except(['create', 'edit']);

/**
 * Route for images
 */
Route::middleware('auth:api')->group(function () {
    Route::get('/ads/{id_ad}/images', [ImageController::class, 'index']);
    Route::post('/images', [ImageController::class, 'store']);
    Route::get('/images/{id_image}', [ImageController::class, 'show']);
    Route::put('/images/{id_image}', [ImageController::class, 'update']);
    Route::delete('/images/{id_image}', [ImageController::class, 'destroy']);
});

// Route::middleware('auth:api')->group(function () {
//     Route::get('/messages', [MessageController::class, 'index']);
//     Route::post('/images', [MessageController::class, 'store']);
//     Route::post('/images{id_message}', [MessageController::class, 'show']);
//     Route::post('/images/{id_message}', [MessageController::class, 'update']);
//     Route::post('/images/{id_message}', [MessageController::class, 'destroy']);
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Route for city
 */
Route::middleware('auth:api')->group(function () {
    Route::get('/locations/cities', [LocationController::class, 'index']);
});

/**
 * Route for favorites
 */
Route::middleware('auth:api')->group(function () {
    Route::get('/favoris', [FavoriController::class, 'index']);
    Route::post('/favoris', [FavoriController::class, 'store']);
    Route::get('/favoris/{id_favoris}', [FavoriController::class, 'show']);
    Route::delete('/favoris/{id_favoris}', [FavoriController::class, 'destroy']);
});
