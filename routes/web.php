<?php

use App\Http\Controllers\KepekController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
