<?php

namespace App\Http\Controllers\authentication\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LoginInputBoundary;

class LoginController extends Controller
{
    public function login(Request $request, LoginInputBoundary $loginInputBoundary)
    {
        if ($request->method() === 'post') {
            return;
        }

        $output = $loginInputBoundary->login(
            $request->input('email'),
            $request->input('password'),
            $request->input('client_id'),
            $request->input('redirect_url'),
            $request->input('response_type')
        );

        $output->response();
    }
}