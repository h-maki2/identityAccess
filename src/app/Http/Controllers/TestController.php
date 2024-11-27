<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use packages\domain\model\authenticationInformation\UserEmail;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $email = $request->query('email');
        $userEmail = new UserEmail($email);

        return response()->json([
            'email' => $userEmail->value
        ], 200);
    }
}