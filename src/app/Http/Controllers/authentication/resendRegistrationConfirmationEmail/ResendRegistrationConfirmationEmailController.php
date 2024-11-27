<?php

namespace App\Http\Controllers\authentication\ResendRegistrationConfirmationEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailInputBoundary;

class ResendRegistrationConfirmationEmailController extends Controller
{
    private ResendRegistrationConfirmationEmailInputBoundary $ResendRegistrationConfirmationEmailInputBoundary;

    public function __construct(ResendRegistrationConfirmationEmailInputBoundary $ResendRegistrationConfirmationEmailInputBoundary)
    {
        $this->ResendRegistrationConfirmationEmailInputBoundary = $ResendRegistrationConfirmationEmailInputBoundary;
    }

    public function resendRegistrationConfirmationEmail(Request $request)
    {
        $output = $this->ResendRegistrationConfirmationEmailInputBoundary->resendRegistrationConfirmationEmail(
            $request->input('email')
        );

        return $output->response();
    }
}