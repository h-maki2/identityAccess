<?php

namespace App\Http\Controllers\Api\V1\authentication\resendRegistrationConfirmationEmail;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json\JsonResendRegistrationConfirmationEmailPresenter;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailInputBoundary;

class ResendRegistrationConfirmationEmailController extends Controller
{
    private ResendRegistrationConfirmationEmailInputBoundary $ResendRegistrationConfirmationEmailInputBoundary;

    public function __construct(ResendRegistrationConfirmationEmailInputBoundary $ResendRegistrationConfirmationEmailInputBoundary)
    {
        $this->ResendRegistrationConfirmationEmailInputBoundary = $ResendRegistrationConfirmationEmailInputBoundary;
    }

    public function resendRegistrationConfirmationEmail(Request $request): JsonResponse
    {
        $output = $this->ResendRegistrationConfirmationEmailInputBoundary->resendRegistrationConfirmationEmail(
            $request->input('email')
        );

        $presenter = new JsonResendRegistrationConfirmationEmailPresenter($output);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}