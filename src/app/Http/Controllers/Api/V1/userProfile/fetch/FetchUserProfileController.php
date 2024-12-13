<?php

namespace packages\adapter\http\api\v1\userProfile\fetch;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use packages\adapter\presenter\userProfile\fetch\json\JsonFetchUserProfilePresenter;
use packages\application\userProfile\fetch\FetchUserProfileInputBoundary;

class FetchUserProfileController extends Controller
{
    private FetchUserProfileInputBoundary $fetchUserProfileInputBoundary;

    public function __construct(FetchUserProfileInputBoundary $fetchUserProfileInputBoundary)
    {
        $this->fetchUserProfileInputBoundary = $fetchUserProfileInputBoundary;
    }

    public function fetch(Request $request): JsonResponse
    {
        $result = $this->fetchUserProfileInputBoundary->handle($request->query('scope', ''));

        $presenter = new JsonFetchUserProfilePresenter($result);
        $jsonResponseData = $presenter->jsonResponseData();
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}