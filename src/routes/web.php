<?php

use App\Http\Controllers\Web\authentication\login\LoginController;
use App\Http\Controllers\Web\registration\DefinitiveRegistrationController;
use App\Http\Controllers\Web\registration\ProvisionalRegistrationController;
use App\Http\Controllers\Web\registration\ResendDefinitiveRegistrationConfirmation;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

Route::get('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegisterForm']);
Route::post('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompleted']);

Route::get('/resend', [ResendDefinitiveRegistrationConfirmation::class, 'resendDefinitiveRegistrationConfirmationForm']);
Route::post('/resend', [ResendDefinitiveRegistrationConfirmation::class, 'resendDefinitiveRegistrationConfirmation']);

Route::get('/login', [LoginController::class, 'displayLoginForm']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});