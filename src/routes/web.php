<?php

use App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted\definitiveRegistrationCompletedController;
use App\Http\Controllers\Web\userRegistration\UserRegistrationController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/userRegistration', [UserRegistrationController::class, 'userRegisterForm']);
Route::post('/userRegistration', [UserRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegistrationCompleted', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegistrationCompleted', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompleted']);

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});