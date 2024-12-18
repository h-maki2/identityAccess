<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

interface DefinitiveRegistrationConfirmedUpdateInputBoundary
{
    public function DefinitiveRegistrationConfirmedUpdate(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): DefinitiveRegistrationConfirmedUpdateResult;
}