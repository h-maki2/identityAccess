<?php

namespace App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedPresenter;
use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\json\JsonDefinitiveRegistrationCompletedPresenter;
use packages\adapter\view\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedView;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedInputBoundary;

class DefinitiveRegistrationCompletedController extends Controller
{
    public function DefinitiveRegistrationCompletedForm(Request $request)
    {
        return view('authentication.DefinitiveRegistrationCompleted.DefinitiveRegistrationCompletedForm', [
            'oneTimeToken' => $request->query('token', ''),
        ]);
    }

    public function DefinitiveRegistrationCompleted(
        Request $request,
        DefinitiveRegistrationCompletedInputBoundary $DefinitiveRegistrationCompletedInputBoundary
    )
    {
        $output = $DefinitiveRegistrationCompletedInputBoundary->DefinitiveRegistrationCompleted(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeDefinitiveRegistrationCompletedPresenter($output);
        $view = new BladeDefinitiveRegistrationCompletedView($presenter);
        return $view->response();
    }
}