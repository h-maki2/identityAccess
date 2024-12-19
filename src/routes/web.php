<?php

use App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedController;
use App\Http\Controllers\Web\UserProvisionalRegistration\UserProvisionalRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/UserProvisionalRegistration', [UserProvisionalRegistrationController::class, 'userRegisterForm']);
Route::post('/UserProvisionalRegistration', [UserProvisionalRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegistrationComplete', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegistrationComplete', [DefinitiveRegistrationCompletedController::class, 'definitiveRegistrationCompleted']);

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});