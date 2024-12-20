<?php

namespace packages\application\registration\provisionalRegistration;

interface ProvisionalRegistrationInputBoundary
{
    public function userRegister(
        string $email,
        string $password,
        string $passwordConfirmation
    ): ProvisionalRegistrationResult;
}