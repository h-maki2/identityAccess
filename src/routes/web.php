<?php

use App\Http\Controllers\Web\registration\DefinitiveRegistrationController;
use App\Http\Controllers\Web\registration\ProvisionalRegistrationController;
use App\Http\Controllers\Web\registration\ResendDefinitiveRegistrationConfirmation;
use Illuminate\Support\Facades\Route;

Route::get('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegisterForm']);
Route::post('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompleted']);

Route::get('/resend', [ResendDefinitiveRegistrationConfirmation::class, 'resendDefinitiveRegistrationConfirmationForm']);
Route::post('/resend', [ResendDefinitiveRegistrationConfirmation::class, 'resendDefinitiveRegistrationConfirmation']);

Route::middleware(['auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});