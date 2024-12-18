<?php

namespace App\Http\Controllers\Web\authentication\definitiveRegistrationCompleted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedUpdatePresenter;
use packages\adapter\view\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedUpdateView;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompleteInputBoundary;

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
        DefinitiveRegistrationCompleteInputBoundary $DefinitiveRegistrationCompleteInputBoundary
    )
    {
        $output = $DefinitiveRegistrationCompleteInputBoundary->handle(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeDefinitiveRegistrationCompletedUpdatePresenter($output);
        $view = new BladeDefinitiveRegistrationCompletedUpdateView($presenter);
        return $view->response();
    }
}