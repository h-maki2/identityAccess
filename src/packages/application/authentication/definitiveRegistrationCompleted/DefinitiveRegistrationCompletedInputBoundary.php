<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

interface DefinitiveRegistrationCompletedInputBoundary
{
    public function DefinitiveRegistrationCompleted(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): DefinitiveRegistrationCompletedResult;
}