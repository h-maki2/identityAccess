<?php

namespace App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedPresenter;
use packages\adapter\view\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedView;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedInputBoundary;

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
        DefinitiveRegistrationCompletedInputBoundary $definitiveRegistrationCompletedInputBoundary
    )
    {
        $output = $definitiveRegistrationCompletedInputBoundary->DefinitiveRegistrationCompleted(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeDefinitiveRegistrationCompletedPresenter($output);
        $view = new BladeDefinitiveRegistrationCompletedView($presenter);
        return $view->response();
    }
}