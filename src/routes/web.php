<?php

// use App\Http\Controllers\LoginController;

// use App\Http\Controllers\authentication\login\LoginController;

use App\Http\Controllers\Web\userRegistration\UserRegistrationController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/userRegistration', [UserRegistrationController::class, 'userRegisterForm']);
Route::post('/userRegistration', [UserRegistrationController::class, 'userRegister'])->withoutMiddleware(ValidateCsrfToken::class);;

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});