<?php

use App\Http\Controllers\Api\Apartments\ApartmentController;
use App\Http\Controllers\Api\Favorites\FavoritesController;
use App\Http\Controllers\Api\Rating\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('apartments')->group(function () {
    
    Route::get('/index', [ApartmentController::class, 'index'])->name('apartments.index'); 
    Route::get('/show/{id}', [ApartmentController::class, 'show'])->name('apartments.show');
    Route::get('/filter', [ApartmentController::class, 'filter'])->name('apartments.filter');
});


Route::prefix('apartments')->middleware('auth:sanctum')->group(function () {
    
    Route::post('/store', [ApartmentController::class, 'store'])->name('apartments.store');
    
    Route::post('/update/{id}', [ApartmentController::class, 'update']);
    
    Route::delete('/delete/{id}', [ApartmentController::class, 'destroy']);
});


//==============================================================================================================
//={Favorites}=================================================================================================
//==============================================================================================================

Route::prefix('favorites')->middleware('auth:sanctum')->group(function () {
    
    Route::post('/add', [FavoritesController::class, 'storefavorite']);
    
    Route::delete('/remove', [FavoritesController::class, 'removefavorite']);
    
    Route::get('/list', [FavoritesController::class, 'GetAllFavorites']);
});

//==============================================================================================================
//={Rating}=================================================================================================
//==============================================================================================================
Route::prefix('rating')->middleware('auth:sanctum')->group(function () {

    Route::get('/list', [RatingController::class, 'GetAllRatings']);
    
    Route::post('/add', [RatingController::class, 'storerate']);
    
    Route::delete('/remove', [RatingController::class, 'destroyRating']);
    
    Route::post('/update', [RatingController::class, 'updateRating']);
    
});