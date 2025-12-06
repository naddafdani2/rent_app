<?php

use App\Http\Controllers\Api\Apartments\ApartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('apartments')->middleware('auth:sanctum')->group(function () {
    Route::get('/index', [ApartmentController::class, 'index']);
    Route::get('/show/{id}', [ApartmentController::class, 'show']);
    Route::post('/store', [ApartmentController::class, 'store']);
    Route::post('/update/{id}', [ApartmentController::class, 'update']);
    Route::delete('/delete/{id}', [ApartmentController::class, 'destroy']);
    Route::get('/filter', [ApartmentController::class, 'filter']);
});