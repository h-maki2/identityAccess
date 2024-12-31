<?php

namespace App\Http\Controllers\Api\v1\logout;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\authentication\logout\LogoutApplicationService;

class LogoutController extends Controller
{
    public function logout(
        LogoutApplicationService $logout,
        Request $request
    ): JsonResponse
    {
        $logout->logout(
            $request->bearerToken()
        );

        $jsonResponse = new JsonResponseData([], HttpStatus::Success);
        return response()->json($jsonResponse->value, $jsonResponse->httpStatusCode());
    }
}