<?php

namespace packages\application\authentication\UserDefinitiveRegistration;

interface UserDefinitiveRegistrationInputBoundary
{
    public function handle(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): UserDefinitiveRegistrationResult;
}