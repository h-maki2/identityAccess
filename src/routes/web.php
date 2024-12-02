<?php

// use App\Http\Controllers\LoginController;

// use App\Http\Controllers\authentication\login\LoginController;

use App\Http\Controllers\authentication\login\LoginController;
use App\Http\Controllers\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailController;
use App\Http\Controllers\authentication\verifiedUpdate\DisplayVerifiedUpdatePageController;
use App\Http\Controllers\authentication\verifiedUpdate\VerifiedUpdateController;
use App\Http\Controllers\userRegistration\UserRegistrationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::post('/resendRegistrationConfirmationEmail', [ResendRegistrationConfirmationEmailController::class, 'resendRegistrationConfirmationEmail']);

Route::get('/verifiedUpdate', [DisplayVerifiedUpdatePageController::class, 'displayVerifiedUpdatePage']);
Route::post('/verifiedUpdate', [VerifiedUpdateController::class, 'verifiedUpdate']);

Route::post('/userRegistration', [UserRegistrationController::class, 'userRegister']);