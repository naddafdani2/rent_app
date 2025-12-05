<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::get('/Auth', function (Request $request) {
//     return 'welcome Users';
// }   );

Route::apiResource('user',UserController::class);
