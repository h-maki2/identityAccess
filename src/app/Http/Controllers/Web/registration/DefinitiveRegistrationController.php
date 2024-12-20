<?php

namespace App\Http\Controllers\Web\registration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\registration\definitiveRegistration\blade\BladeDefinitiveRegistrationPresenter;
use packages\adapter\view\registration\definitiveRegistration\blade\BladeDefinitiveRegistrationView;
use packages\application\registration\definitiveRegistration\DefinitiveRegistrationInputBoundary;

class DefinitiveRegistrationController extends Controller
{
    public function definitiveRegistrationCompletedForm(Request $request)
    {
        return view('registration.definitiveRegistration.definitiveRegistrationCompletedForm', [
            'oneTimeToken' => $request->query('token', ''),
        ]);
    }

    public function definitiveRegistrationCompleted(
        Request $request,
        DefinitiveRegistrationInputBoundary $definitiveRegistrationInputBoundary
    )
    {
        $output = $definitiveRegistrationInputBoundary->handle(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeDefinitiveRegistrationPresenter($output);
        $view = new BladeDefinitiveRegistrationView($presenter);
        return $view->response();
    }
}