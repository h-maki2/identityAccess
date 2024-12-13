<?php

namespace packages\application\authentication\login;

interface LoginInputBoundary
{
    /**
     * ログインする
     */
    public function login(
        string $inputedEmail,
        string $inputedPassword,
        string $clientId,
        string $redirectUrl,
        string $responseType,
        string $state,
        string $scope
    ): LoginResult;
}