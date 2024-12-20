<?php

namespace App\Http\Controllers\Web\registration;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json\JsonResendRegistrationConfirmationEmailPresenter;
use packages\adapter\presenter\registration\resendDefinitiveRegistrationConfirmation\blade\BladeResendDefinitiveRegistrationConfirmationPresenter;
use packages\adapter\view\registration\resendDefinitiveRegistrationConfirmation\BladeResendDefinitiveRegistrationConfirmationView;
use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationInputBoundary;

class ResendDefinitiveRegistrationConfirmation extends Controller
{
    public function resendDefinitiveRegistrationConfirmationForm()
    {
        return view('registration.resendDefinitiveRegistrationConfirmation.resendDefinitiveRegistrationConfirmationForm');
    }

    public function resendDefinitiveRegistrationConfirmation(
        Request $request, 
        ResendDefinitiveRegistrationConfirmationInputBoundary $resendDefinitiveRegistrationConfirmationInputBoundary
    ) {
        $output = $resendDefinitiveRegistrationConfirmationInputBoundary->handle(
            $request->input('email') ?? ''
        );

        $presenter = new BladeResendDefinitiveRegistrationConfirmationPresenter($output);
        $view = new BladeResendDefinitiveRegistrationConfirmationView($presenter);
        return $view->response();
    }
}