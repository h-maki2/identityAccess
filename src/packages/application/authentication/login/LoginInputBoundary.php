<?php

use packages\application\authentication\login\LoginOutputBoundary;

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
        string $responseType
    ): LoginOutputBoundary;
}