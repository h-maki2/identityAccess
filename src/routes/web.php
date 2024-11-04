<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return view('welcome');
});

Route::get('/auth', [LoginController::class, 'index'])->name('login');

Route::get('/test/token', [LoginController::class, 'token']);