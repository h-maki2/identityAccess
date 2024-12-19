<?php

namespace packages\application\registration\userProvisionalRegistration;

interface UserProvisionalRegistrationInputBoundary
{
    public function userRegister(
        string $email,
        string $password,
        string $passwordConfirmation
    ): UserProvisionalRegistrationResult;
}