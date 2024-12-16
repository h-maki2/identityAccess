<?php

namespace App\Http\Controllers\Web\authentication\verifiedUpdate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\verifiedUpdate\blade\BladeVerifiedUpdatePresenter;
use packages\adapter\presenter\authentication\verifiedUpdate\json\JsonVerifiedUpdatePresenter;
use packages\adapter\view\authentication\verifiedUpdate\blade\BladeVerifiedUpdateView;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateInputBoundary;

class VerifiedUpdateController extends Controller
{
    public function verifiedUpdateForm(Request $request)
    {
        return view('authentication.verifiedUpdate.verifiedUpdateForm', [
            'oneTimeToken' => $request->query('token', ''),
        ]);
    }

    public function verifiedUpdate(
        Request $request,
        VerifiedUpdateInputBoundary $verifiedUpdateInputBoundary
    )
    {
        $output = $verifiedUpdateInputBoundary->verifiedUpdate(
            $request->input('oneTimeToken') ?? '',
            $request->input('oneTimePassword') ?? ''
        );

        $presenter = new BladeVerifiedUpdatePresenter($output);
        $view = new BladeVerifiedUpdateView($presenter);
        return $view->response();
    }
}