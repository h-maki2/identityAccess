<?php

namespace App\Http\Controllers\Web\authentication\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\authentication\login\blade\BladeLoginPresenter;
use packages\adapter\presenter\authentication\login\json\JsonLoginPresenter;
use packages\adapter\view\authentication\login\blade\BladeLoginView;
use packages\application\authentication\displayLoginForm\DisplayLoginFormApplicationService;
use packages\application\authentication\login\LoginInputBoundary;

class LoginController extends Controller
{
    public function displayLoginForm(Request $request, DisplayLoginFormApplicationService $displayLoginForm)
    {
        $result = $displayLoginForm->handle(
            $request->query('client_id', ''),
            $request->query('redirect_url', ''),
            $request->query('response_type', ''),
            $request->query('state', ''),
            $request->query('scope', '')
        );
        return view('authentication.login', [
            'clientId' => $result->clientId,
            'redirectUrl' => $result->redirectUrl,
            'responseType' => $result->responseType,
            'state' => $result->state,
            'scopes' => $result->scopes
        ]);
    }

    public function login(Request $request, LoginInputBoundary $loginInputBoundary)
    {
        $result = $loginInputBoundary->login(
            $request->input('email') ?? '',
            $request->input('password') ?? '',
            $request->input('client_id') ?? '',
            $request->input('redirect_url') ?? '',
            $request->input('response_type') ?? '',
            $request->input('state') ?? '',
            $request->input('scope') ?? ''
        );

        $presenter = new BladeLoginPresenter($result);
        $view = new BladeLoginView($presenter);
        return $view->response();
    }
}