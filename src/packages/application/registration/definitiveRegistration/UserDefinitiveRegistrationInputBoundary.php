<?php

namespace packages\application\registration\definitiveRegistration;

interface UserDefinitiveRegistrationInputBoundary
{
    public function handle(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): UserDefinitiveRegistrationResult;
}