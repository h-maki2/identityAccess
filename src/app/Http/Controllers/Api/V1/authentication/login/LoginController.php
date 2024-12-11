<?php

namespace App\Http\Controllers\Api\V1\authentication\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\login\json\JsonLoginPresenter;
use packages\application\authentication\login\LoginInputBoundary;

class LoginController extends Controller
{
    private LoginInputBoundary $loginInputBoundary;

    public function __construct(LoginInputBoundary $loginInputBoundary)
    {
        $this->loginInputBoundary = $loginInputBoundary;
    }

    public function login(Request $request): JsonResponse
    {
        $output = $this->loginInputBoundary->login(
            $request->input('email'),
            $request->input('password'),
            $request->input('client_id'),
            $request->input('redirect_url'),
            $request->input('response_type'),
            $request->input('state')
        );

        $presenter = new JsonLoginPresenter($output);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}