<?php

namespace App\Http\Controllers\Web\registration;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use packages\adapter\presenter\registration\provisionalRegistration\blade\BladeUserProvisionalRegistrationPresenter;
use packages\adapter\view\registration\provisionalRegistration\blade\BladeUserProvisionalRegistrationView;
use packages\application\registration\provisionalRegistration\UserProvisionalRegistrationInputBoundary;

class UserProvisionalRegistrationController extends Controller
{
    public function userRegisterForm(): View
    {
        return view('registration.provisionalRegistration.userProvisionalRegistrationForm');
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