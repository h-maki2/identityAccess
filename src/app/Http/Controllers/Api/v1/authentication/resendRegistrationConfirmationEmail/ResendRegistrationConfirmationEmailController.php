<?php

namespace App\Http\Controllers\Api\v1\authentication\resendRegistrationConfirmationEmail;

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

    public function resendRegistrationConfirmationEmail(Request $request): mixed
    {
        $output = $this->ResendRegistrationConfirmationEmailInputBoundary->resendRegistrationConfirmationEmail(
            $request->input('email')
        );

        return $output->response();
    }
}