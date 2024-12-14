<?php

namespace App\Http\Controllers\Api\V1\authentication\verifiedUpdate;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\verifiedUpdate\json\JsonVerifiedUpdatePresenter;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateInputBoundary;

class VerifiedUpdateController extends Controller
{
    private VerifiedUpdateInputBoundary $verifiedUpdateInputBoundary;

    public function __construct(VerifiedUpdateInputBoundary $verifiedUpdateInputBoundary)
    {
        $this->verifiedUpdateInputBoundary = $verifiedUpdateInputBoundary;
    }

    public function verifiedUpdate(Request $request): JsonResponse
    {
        $output = $this->verifiedUpdateInputBoundary->verifiedUpdate(
            $request->input('oneTimeToken', ''),
            $request->input('oneTimePassword', '')
        );

        $presenter = new JsonVerifiedUpdatePresenter($output);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}