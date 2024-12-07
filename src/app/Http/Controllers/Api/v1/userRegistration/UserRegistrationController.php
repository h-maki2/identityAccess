<?php

namespace App\Http\Controllers\Api\v1\userRegistration;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use packages\application\userRegistration\UserRegistrationInputBoundary;

class UserRegistrationController extends Controller
{
    private UserRegistrationInputBoundary $userRegistrationInputBoundary;

    public function __construct(UserRegistrationInputBoundary $userRegistrationInputBoundary)
    {
        $this->userRegistrationInputBoundary = $userRegistrationInputBoundary;
    }

    public function userRegister(Request $request): mixed
    {
        $output = $this->userRegistrationInputBoundary->userRegister(
            $request->input('email'),
            $request->input('password')
        );

        return $output->response();
    }
}