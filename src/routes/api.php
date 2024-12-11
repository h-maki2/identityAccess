<?php

use App\Services\ApiVersionResolver;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.version'])->group(function () {
    Route::post('/verifiedUpdate', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'authentication\verifiedUpdate\VerifiedUpdateController');
        return $container->call([$controller, 'verifiedUpdate']);
    });

    Route::post('/login', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'authentication\login\LoginController');

        return $container->call([$controller, 'login']);
    });

    Route::post('/resendRegistrationConfirmationEmail', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailController');

        return $container->call([$controller, 'resendRegistrationConfirmationEmail']);
    });

    Route::post('/userRegistration', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'userRegistration\UserRegistrationController');

        return $container->call([$controller, 'userRegister']);
    });
});

Route::middleware(['api.version', 'auth:api'])->group(function () {
    Route::post('/userProfile/register', function (Request $request, ApiVersionResolver $resolver, Container $container) {
        $version = $request->attributes->get('api_version');
        $controller = $resolver->resolve($version, 'userProfile\register\RegisterUserProfileController');
        return $container->call([$controller, 'register']);
    });
});
