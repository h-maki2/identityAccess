<?php

use App\Http\Controllers\Api\V1\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailController;
use App\Http\Controllers\Web\registration\DefinitiveRegistrationController;
use App\Http\Controllers\Web\registration\ProvisionalRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegisterForm']);
Route::post('/provisionalRegister', [ProvisionalRegistrationController::class, 'userRegister']);

Route::get('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompletedForm']);
Route::post('/definitiveRegister', [DefinitiveRegistrationController::class, 'definitiveRegistrationCompleted']);

Route::get('/resend', [ResendRegistrationConfirmationEmailController::class, 'resendDefinitiveRegistrationConfirmationForm']);
Route::post('/resend', [ResendRegistrationConfirmationEmailController::class, 'resendDefinitiveRegistrationConfirmation']);

Route::middleware(['api.version', 'auth:api'])->group(function () {
    // 認証が必要なAPIのルーティング
});