<?php

use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\test\helpers\authConfirmation\TestAuthConfirmationFactory;
use packages\test\helpers\authConfirmation\TestOneTimeTokenFactory;
use PHPUnit\Framework\TestCase;

class AuthConfirmationValidationTest extends TestCase
{
    private AuthConfirmationValidation $authConfirmationValidation;

    public function setUp(): void
    {
        $this->authConfirmationValidation = new AuthConfirmationValidation(
            new InMemoryAuthConfirmationRepository()
        );
    }

    public function test_無効なワンタイムパスワードの場合はfalseを返す()
    {
        
    }
}