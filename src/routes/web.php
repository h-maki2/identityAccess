<?php

use App\Http\Controllers\Web\registration\UserDefinitiveRegistrationController;
use App\Http\Controllers\Web\registration\UserProvisionalRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/UserProvisionalRegistration', [UserProvisionalRegistrationController::class, 'userRegisterForm']);
Route::post('/UserProvisionalRegistration', [UserProvisionalRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegistrationComplete', [UserDefinitiveRegistrationController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegistrationComplete', [UserDefinitiveRegistrationController::class, 'definitiveRegistrationCompleted']);

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});