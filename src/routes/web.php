<?php

use App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedController;
use App\Http\Controllers\Web\userRegistration\UserRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/userRegistration', [UserRegistrationController::class, 'userRegisterForm']);
Route::post('/userRegistration', [UserRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegistrationComplete', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegistrationComplete', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompleted']);

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});