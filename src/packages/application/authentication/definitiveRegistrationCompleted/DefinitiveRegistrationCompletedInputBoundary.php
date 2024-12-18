<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

interface DefinitiveRegistrationCompletedInputBoundary
{
    public function handle(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): DefinitiveRegistrationCompletedUpdateResult;
}