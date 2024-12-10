<?php

namespace App\Http\Controllers\Api\V1\userProfile\register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\application\userProfile\register\RegisterUserProfileInputBoundary;

class RegisterUserProfileController extends Controller
{
    private RegisterUserProfileInputBoundary $registerUserProfileInputBoundary;

    public function __construct(RegisterUserProfileInputBoundary $registerUserProfileInputBoundary)
    {
        $this->registerUserProfileInputBoundary = $registerUserProfileInputBoundary;
    }

    /**
     * ユーザー登録を行う
     */
    public function register(Request $request): mixed
    {
        $result = $this->registerUserProfileInputBoundary->register(
            $request->input('userName', ''),
            $request->input('selfIntroductionText', '')
        );

        return $result->response();
    }
}