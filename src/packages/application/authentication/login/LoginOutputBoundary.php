<?php

namespace packages\application\authentication\login;

interface LoginOutputBoundary
{
    public function formatForResponse(LoginResult $loginResult): void;
    public function response(): mixed;
}