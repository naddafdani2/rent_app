<?php

use App\Http\Controllers\Api\AdminDashboard\AdminController;
use App\Http\Controllers\Api\AdminDashboard\AdminRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth:sanctum')->group(function(){
   Route::post('/apartments/{apartmentId}/conditions',[AdminController::class,'apartmentsConditions']);
   Route::post('/Users/{userId}/conditions',[AdminController::class,'usersConditions']); 
   Route::get('/apartments/all',[AdminController::class,'adminApartmentsIndex']);
   Route::get('/users/all',[AdminController::class,'adminUsersIndex']);
});

//==============================================================================================================
//==={Admin Records}=============================================================================================
//==============================================================================================================


Route::prefix('admin/records')->middleware('auth:sanctum')->group(function(){
    Route::get('/all',[AdminRecords::class,'GetAllRecords']);
    Route::get('/type/{targetType}',[AdminRecords::class,'GetRecordsByType']);
    Route::post('/update/{recordId}',[AdminRecords::class,'updateRecord']);
    Route::delete('/delete/{recordId}',[AdminRecords::class,'deleteRecord']);

});