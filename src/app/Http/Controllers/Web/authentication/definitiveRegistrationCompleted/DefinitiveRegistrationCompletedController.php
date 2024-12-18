<?php

namespace App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationConfirmedUpdatePresenter;
use packages\adapter\view\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationConfirmedUpdateView;
use packages\application\authentication\definitiveRegistrationCompleted\definitiveRegistrationConfirmedUpdateInputBoundary;

class DefinitiveRegistrationCompletedController extends Controller
{
    public function definitiveRegistrationCompletedForm(Request $request)
    {
        return view('authentication.definitiveRegistrationCompleted.definitiveRegistrationCompletedForm', [
            'oneTimeToken' => $request->query('token', ''),
        ]);
    }

    public function definitiveRegistrationCompleted(
        Request $request,
        DefinitiveRegistrationConfirmedUpdateInputBoundary $definitiveRegistrationConfirmedUpdateInputBoundary
    )
    {
        $output = $definitiveRegistrationConfirmedUpdateInputBoundary->DefinitiveRegistrationConfirmedUpdate(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeDefinitiveRegistrationConfirmedUpdatePresenter($output);
        $view = new BladeDefinitiveRegistrationConfirmedUpdateView($presenter);
        return $view->response();
    }
}