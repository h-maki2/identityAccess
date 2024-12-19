<?php

namespace App\Http\Controllers\Api\V1\authentication\resendRegistrationConfirmationEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json\JsonResendRegistrationConfirmationEmailPresenter;
use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationInputBoundary;

class ResendRegistrationConfirmationEmailController extends Controller
{
    private ResendDefinitiveRegistrationConfirmationInputBoundary $resendDefinitiveRegistrationConfirmationInputBoundary;

    public function __construct(ResendDefinitiveRegistrationConfirmationInputBoundary $resendDefinitiveRegistrationConfirmationInputBoundary)
    {
        $this->resendDefinitiveRegistrationConfirmationInputBoundary = $resendDefinitiveRegistrationConfirmationInputBoundary;
    }

    public function resendRegistrationConfirmationEmail(Request $request): JsonResponse
    {
        $output = $this->resendDefinitiveRegistrationConfirmationInputBoundary->resendRegistrationConfirmationEmail(
            $request->input('email')
        );

        $presenter = new JsonResendRegistrationConfirmationEmailPresenter($output);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}