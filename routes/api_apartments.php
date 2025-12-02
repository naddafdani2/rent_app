<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/apartments', function (Request $request) {
    return 'welcome Apartments';
});