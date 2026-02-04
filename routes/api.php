<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('user',UserController::class);

Route::post('/user/{user}', [UserController::class, 'update'])->name('post.user.update');


Route::post('/register',[AuthController::class,'register'])->name('user.register');

Route::post('/login',[AuthController::class,'login'])->name('user.login');

Route::post('/logout',[AuthController::class,'logout'])->name('user.logout')->middleware('auth:sanctum');