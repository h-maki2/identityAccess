<?php

use App\Services\ApiVersionResolver;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.version', 'auth:api'])->group(function () {
    Route::post('/userProfile/register', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'userProfile\register\RegisterUserProfileController');
        return $container->call([$controller, 'register']);
    });
});
