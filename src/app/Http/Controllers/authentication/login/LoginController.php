<?php

namespace App\Http\Controllers\authentication\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\application\authentication\login\LoginInputBoundary;

class LoginController extends Controller
{
    private LoginInputBoundary $loginInputBoundary;

    public function __construct(LoginInputBoundary $loginInputBoundary)
    {
        $this->loginInputBoundary = $loginInputBoundary;
    }

    public function login(Request $request): mixed
    {
        $output = $this->loginInputBoundary->login(
            $request->input('email'),
            $request->input('password'),
            $request->input('client_id'),
            $request->input('redirect_url'),
            $request->input('response_type'),
            $request->input('state')
        );

        return $output->response();
    }
}