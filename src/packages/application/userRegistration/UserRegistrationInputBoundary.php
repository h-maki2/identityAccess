<?php

namespace packages\application\userRegistration;

interface UserRegistrationInputBoundary
{
    public function userRegister(
        string $email,
        string $password,
        string $passwordConfirmation
    ): UserRegistrationResult;
}