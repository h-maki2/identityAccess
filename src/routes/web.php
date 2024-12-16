<?php

// use App\Http\Controllers\LoginController;

// use App\Http\Controllers\authentication\login\LoginController;

use App\Http\Controllers\Api\V1\userRegistration\UserRegistrationController;
use Illuminate\Support\Facades\Route;


Route::post('/userRegistration', [UserRegistrationController::class, 'userRegister']);
Route::get('/userRegistration', [UserRegistrationController::class, 'userRegisterForm']);

Route::middleware(['auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});