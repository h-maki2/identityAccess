<?php

namespace App\Http\Controllers\authentication\oneTimeTokenAndPasswordRegeneration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationInputBoundary;

class OneTimeTokenAndPasswordRegenerationController extends Controller
{
    private OneTimeTokenAndPasswordRegenerationInputBoundary $oneTimeTokenAndPasswordRegenerationInputBoundary;

    public function __construct(OneTimeTokenAndPasswordRegenerationInputBoundary $oneTimeTokenAndPasswordRegenerationInputBoundary)
    {
        $this->oneTimeTokenAndPasswordRegenerationInputBoundary = $oneTimeTokenAndPasswordRegenerationInputBoundary;
    }

    public function regenerateOneTimeTokenAndPassword(Request $request)
    {
        $output = $this->oneTimeTokenAndPasswordRegenerationInputBoundary->regenerateOneTimeTokenAndPassword(
            $request->input('email')
        );

        return $output->response();
    }
}