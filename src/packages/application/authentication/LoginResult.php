<?php

namespace packages\application\authentication;

class LoginResult
{
    private string $authorizationUrl;
    private bool $loginSucceeded;

    private function __construct(
        string $authorizationUrl,
        bool $loginSucceeded
    )
    {
        $this->authorizationUrl = $authorizationUrl;
        $this->loginSucceeded = $loginSucceeded;
    }

    public static function createWhenLoginFailed(): self
    {
        return new self(
            '',
            false
        );
    }

    public static function createWhenLoginSucceeded(string $authorizationUrl): self
    {
        return new self(
            $authorizationUrl,
            true
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
}