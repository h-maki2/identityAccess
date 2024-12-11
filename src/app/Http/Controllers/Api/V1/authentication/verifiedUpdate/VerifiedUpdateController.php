<?php

namespace App\Http\Controllers\Api\V1\authentication\verifiedUpdate;

use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\verifiedUpdate\json\JsonVerifiedUpdatePresenter;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateInputBoundary;

class VerifiedUpdateController
{
    private VerifiedUpdateInputBoundary $verifiedUpdateInputBoundary;

    public function __construct(VerifiedUpdateInputBoundary $verifiedUpdateInputBoundary)
    {
        $this->verifiedUpdateInputBoundary = $verifiedUpdateInputBoundary;
    }

    public function verifiedUpdate(Request $request): mixed
    {
        $output = $this->verifiedUpdateInputBoundary->verifiedUpdate(
            $request->input('oneTimeTokenValue'),
            $request->input('oneTimePassword')
        );

        $presenter = new JsonVerifiedUpdatePresenter($output);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}