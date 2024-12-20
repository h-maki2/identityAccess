<?php

namespace packages\application\registration\definitiveRegistration;

interface DefinitiveRegistrationInputBoundary
{
    public function handle(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): DefinitiveRegistrationResult;
}