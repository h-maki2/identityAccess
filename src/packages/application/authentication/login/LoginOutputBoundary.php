<?php

namespace packages\application\authentication\login;

abstract class LoginOutputBoundary
{
    abstract public function formatForResponse(LoginResult $loginResult): void;
    abstract public function response(): void;
}