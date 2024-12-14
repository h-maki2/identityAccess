<?php

namespace packages\application\authentication\login;

class LoginResult
{
    readonly string $authorizationUrl;
    readonly bool $loginSucceeded;
    readonly bool $accountLocked;

    private function __construct(
        string $authorizationUrl,
        bool $loginSucceeded,
        bool $accountLocked
    )
    {
        $this->authorizationUrl = $authorizationUrl;
        $this->loginSucceeded = $loginSucceeded;
        $this->accountLocked = $accountLocked;
    }

    public static function createWhenLoginFailed(bool $accountLocked): self
    {
        return new self(
            '',
            false,
            $accountLocked
        );
    }

    public static function createWhenLoginSucceeded(string $authorizationUrl): self
    {
        return new self(
            $authorizationUrl,
            true,
            false
        );
    }
}