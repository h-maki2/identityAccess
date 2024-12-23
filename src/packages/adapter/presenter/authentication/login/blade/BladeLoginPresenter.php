<?php

namespace packages\adapter\presenter\authentication\login\blade;

use packages\application\authentication\login\LoginResult;

class BladeLoginPresenter
{
    private LoginResult $result;

    public function __construct(LoginResult $result)
    {
        $this->result = $result;
    }

    public function isLoginSucceeded(): bool
    {
        return $this->result->loginSucceeded;
    }
    
    public function successResponse(): array
    {
        return [
            'authorizationUrl' => $this->result->authorizationUrl
        ];
    }

    public function faildResponse(): array
    {
        return [
            'accountLocked' => $this->result->accountLocked
        ];
    }
}