<?php

namespace App\Http\Controllers\Web\userRegistration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\userRegistration\blade\BladeUserRegistrationPresenter;
use packages\adapter\view\userRegistration\blade\BladeUserRegistrationView;
use packages\application\userRegistration\UserRegistrationInputBoundary;

class UserRegistrationController extends Controller
{
    public function userRegisterForm()
    {
        return view('userRegistration.userRegistrationForm');
    }
    
    public function userRegister(
        UserRegistrationInputBoundary $userRegistrationInputBoundary,
        Request $request
    ): mixed
    {
        $output = $userRegistrationInputBoundary->userRegister(
            $request->input('email', ''),
            $request->input('password', ''),
            $request->input('passwordConfirmation', '')
        );

        $presenter = new BladeUserRegistrationPresenter($output);
        $view = new BladeUserRegistrationView($presenter);
        return $view->response();
    }
}