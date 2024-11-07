<?php

namespace packages\application\authentication;

class LoginResult
{
    private string $errorMessage;
    private string $authorizationUrl;
    private bool $loginSucceeded;

    private function __construct(
        string $errorMessage,
        string $authorizationUrl,
        bool $loginSucceeded
    )
    {
        $this->errorMessage = $errorMessage;
        $this->authorizationUrl = $authorizationUrl;
        $this->loginSucceeded = $loginSucceeded;
    }

    public static function createWhenLoginFailed(string $errorMessage): self
    {
        return new self(
            $errorMessage,
            '',
            false
        );
    }

    public static function createWhenLoginSucceeded(string $authorizationUrl): self
    {
        return new self(
            '',
            $authorizationUrl,
            true
        );
    }

    public function errorMessage(): string
    {
        return $this->errorMessage;
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