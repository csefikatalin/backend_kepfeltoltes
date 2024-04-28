<?php

use App\Http\Controllers\KepekController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('file-upload', [KepekController::class, 'index'])->name('file.upload');
Route::post('file-upload', [KepekController::class, 'store'])->name('file.upload.store');
