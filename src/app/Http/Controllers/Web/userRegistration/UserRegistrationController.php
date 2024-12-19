<?php

namespace App\Http\Controllers\Web\UserProvisionalRegistration;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use packages\adapter\presenter\registration\UserProvisionalRegistration\blade\BladeUserProvisionalRegistrationPresenter;
use packages\adapter\view\UserProvisionalRegistration\blade\BladeUserProvisionalRegistrationView;
use packages\application\registration\userProvisionalRegistration\UserProvisionalRegistrationInputBoundary;

class UserProvisionalRegistrationController extends Controller
{
    public function userRegisterForm(): View
    {
        return view('UserProvisionalRegistration.UserProvisionalRegistrationForm');
    }

    public function userRegister(
        Request $request,
        UserProvisionalRegistrationInputBoundary $userProvisionalRegistrationInputBoundary
    )
    {
        $output = $userProvisionalRegistrationInputBoundary->userRegister(
            $request->input('email') ?? '',
            $request->input('password') ?? '',
            $request->input('passwordConfirmation') ?? ''
        );

        $presenter = new BladeUserProvisionalRegistrationPresenter($output);
        $view = new BladeUserProvisionalRegistrationView($presenter->viewResponse());
        return $view->response();
    }
}