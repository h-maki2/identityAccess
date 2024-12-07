<?php

namespace App\Http\Controllers\Api\V1\authentication\verifiedUpdate;

use Illuminate\Http\Request;
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

        return $output->response();
    }
}