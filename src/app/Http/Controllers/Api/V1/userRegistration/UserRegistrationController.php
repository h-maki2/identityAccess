<?php

namespace App\Http\Controllers\Api\V1\userRegistration;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\userRegistration\json\JsonUserRegistrationPresenter;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\userRegistration\UserRegistrationInputBoundary;

class UserRegistrationController extends Controller
{
    public function userRegister(
        UserRegistrationInputBoundary $userRegistrationInputBoundary,
        JsonUserRegistrationPresenter $userRegistrationPresenter,
        Request $request
    ): mixed
    {
        $output = $userRegistrationInputBoundary->userRegister(
            $request->input('email', ''),
            $request->input('password', ''),
            $request->input('passwordConfirmation', '')
        );

        $jsonPresenter = new JsonUserRegistrationPresenter($output);
        $jsonResponseData = $jsonPresenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}