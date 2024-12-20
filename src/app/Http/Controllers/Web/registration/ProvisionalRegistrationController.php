<?php

namespace App\Http\Controllers\Web\registration;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use packages\adapter\presenter\registration\provisionalRegistration\blade\BladeProvisionalRegistrationPresenter;
use packages\adapter\view\registration\provisionalRegistration\blade\BladeProvisionalRegistrationView;
use packages\application\registration\provisionalRegistration\ProvisionalRegistrationInputBoundary;

class ProvisionalRegistrationController extends Controller
{
    public function userRegisterForm(): View
    {
        return view('registration.provisionalRegistration.provisionalRegistrationForm');
    }

    public function userRegister(
        Request $request,
        ProvisionalRegistrationInputBoundary $provisionalRegistrationUpdateInputBoundary
    )
    {
        $output = $provisionalRegistrationUpdateInputBoundary->userRegister(
            $request->input('email') ?? '',
            $request->input('password') ?? '',
            $request->input('passwordConfirmation') ?? ''
        );

        $presenter = new BladeProvisionalRegistrationPresenter($output);
        $view = new BladeProvisionalRegistrationView($presenter->viewResponse());
        return $view->response();
    }
}