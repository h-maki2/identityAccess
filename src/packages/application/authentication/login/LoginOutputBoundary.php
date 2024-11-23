<?php

namespace packages\application\authentication\login;

interface LoginOutputBoundary
{
    public function present(LoginResult $loginResult): void;
}