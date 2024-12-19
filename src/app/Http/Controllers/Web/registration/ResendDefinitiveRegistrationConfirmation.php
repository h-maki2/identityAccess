<?php

namespace App\Http\Controllers\Api\V1\authentication\resendRegistrationConfirmationEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json\JsonResendRegistrationConfirmationEmailPresenter;
use packages\adapter\presenter\registration\resendDefinitiveRegistrationConfirmation\blade\BladeResendDefinitiveRegistrationConfirmationPresenter;
use packages\adapter\view\registration\resendDefinitiveRegistrationConfirmation\BladeResendDefinitiveRegistrationConfirmationView;
use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationInputBoundary;

class ResendRegistrationConfirmationEmailController extends Controller
{
    private ResendDefinitiveRegistrationConfirmationInputBoundary $resendDefinitiveRegistrationConfirmationInputBoundary;

    public function __construct(ResendDefinitiveRegistrationConfirmationInputBoundary $resendDefinitiveRegistrationConfirmationInputBoundary)
    {
        $this->resendDefinitiveRegistrationConfirmationInputBoundary = $resendDefinitiveRegistrationConfirmationInputBoundary;
    }

    public function resendRegistrationConfirmationEmail(Request $request)
    {
        $output = $this->resendDefinitiveRegistrationConfirmationInputBoundary->handle(
            $request->input('email') ?? ''
        );

        $presenter = new BladeResendDefinitiveRegistrationConfirmationPresenter($output);
        $view = new BladeResendDefinitiveRegistrationConfirmationView($presenter);
        return $view->response();
    }
}