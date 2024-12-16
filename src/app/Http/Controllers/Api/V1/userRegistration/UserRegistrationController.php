<?php

namespace App\Http\Controllers\Api\V1\userRegistration;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\userRegistration\json\JsonUserRegistrationPresenter;
use packages\adapter\view\userRegistration\json\JsonUserRegistrationView;
use packages\application\userRegistration\UserRegistrationInputBoundary;

class UserRegistrationController extends Controller
{
    private UserRegistrationInputBoundary $userRegistrationInputBoundary;

    public function __construct(UserRegistrationInputBoundary $userRegistrationInputBoundary)
    {
        $this->userRegistrationInputBoundary = $userRegistrationInputBoundary;
    }

    public function userRegister(Request $request): JsonResponse
    {
        $output = $this->userRegistrationInputBoundary->userRegister(
            $request->input('email', ''),
            $request->input('password', ''),
            $request->input('passwordConfirmation', '')
        );

        $presenter = new JsonUserRegistrationPresenter($output);
        $view = new JsonUserRegistrationView($presenter->viewResponse());
        return $view->response();
    }
}