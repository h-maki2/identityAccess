<?php

namespace packages\application\authentication\login;

abstract class LoginOutputBoundary
{
    abstract protected function __construct(LoginResult $loginResult);
    abstract public static function create(LoginResult $loginResult): self;
    abstract public function response(): void;
}