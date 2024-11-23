<?php

namespace packages\application\authentication\login;

abstract class LoginOutputBoundary
{
    abstract public function present(LoginResult $loginResult): void;
    abstract public function response();
}