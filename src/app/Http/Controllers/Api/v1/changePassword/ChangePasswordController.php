<?php

namespace App\Http\Controllers\Api\v1\changePassword;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\changePassword\json\JsonChangePasswordPresenter;
use packages\application\changePassword\ChangePasswordApplicationInputBoundary;

class ChangePasswordController extends Controller
{
    public function changePassword(
        Request $request, 
        ChangePasswordApplicationInputBoundary $changePasswordApplicationInputBoundary
    ): JsonResponse
    {
        $result = $changePasswordApplicationInputBoundary->changePassword(
            $request->input('scope', ''),
            $request->input('password', '')
        );

        $presenter = new JsonChangePasswordPresenter($result);
        return response()->json($presenter->jsonResponseData());
    }
}