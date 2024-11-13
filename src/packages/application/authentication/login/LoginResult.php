<?php

namespace packages\application\authentication\login;

class LoginResult
{
    private string $authorizationUrl;
    private bool $loginSucceeded;
    private bool $accountLocked;

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
    
    public function authorizationUrl(): string
    {
        return $this->authorizationUrl;
    }

    public function loginSucceeded(): bool
    {
        return $this->loginSucceeded;
    }

    public function accountLocked(): bool
    {
        return $this->accountLocked;
    }
}