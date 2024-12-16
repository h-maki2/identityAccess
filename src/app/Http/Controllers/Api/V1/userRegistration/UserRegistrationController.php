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
    private UserRegistrationInputBoundary $userRegistrationInputBoundary;
    private UserRegistrationPresenter $userRegistrationPresenter;

    public function __construct(
        UserRegistrationInputBoundary $userRegistrationInputBoundary,
        UserRegistrationPresenter $userRegistrationPresenter
    )
    {
        $this->userRegistrationInputBoundary = $userRegistrationInputBoundary;
        $this->userRegistrationPresenter = $userRegistrationPresenter;
    }

    public function userRegister(Request $request): mixed
    {
        $output = $this->userRegistrationInputBoundary->userRegister(
            $request->input('email', ''),
            $request->input('password', ''),
            $request->input('passwordConfirmation', '')
        );

        $this->userRegistrationPresenter->setResult($output);
        return $this->userRegistrationPresenter->responseView();
    }
}