<?php

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('book')->middleware('auth:sanctum')->group(function(){

    Route::get('/index',[BookingController::class,'index'])->name('book.index');

    Route::get('/show/{role}/{status?}',[BookingController::class,'show'])
    ->where('status','all|accepted|modified|cancelled')->name('book.showAll');

    Route::get('/showSpecificBooking/{id}',[BookingController::class,'showSpecificBooking'])
    ->name('book.showSpecific');

    Route::post('/create',[BookingController::class,'create'])->name('book.create');

    Route::post('/update/{id}',[BookingController::class,'update'])->name('book.update');

    Route::delete('/cancel/{id}',[BookingController::class,'cancel'])->name('book.cancel');
});

Route::get('book/ShowOneApartmentBooking/{id}/{status?}',[BookingController::class,'ShowOneApartmentBooking'])
->where('status','all|accepted|modified|cancelled')->name('book.ShowAptBook');
