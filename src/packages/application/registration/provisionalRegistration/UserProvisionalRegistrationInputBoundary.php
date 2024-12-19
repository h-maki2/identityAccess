<?php

namespace packages\application\registration\provisionalRegistration;

interface UserProvisionalRegistrationInputBoundary
{
    public function userRegister(
        string $email,
        string $password,
        string $passwordConfirmation
    ): UserProvisionalRegistrationResult;
}