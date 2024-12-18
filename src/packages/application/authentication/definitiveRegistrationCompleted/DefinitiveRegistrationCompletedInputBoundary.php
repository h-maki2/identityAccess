<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

interface DefinitiveRegistrationCompleteInputBoundary
{
    public function handle(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): DefinitiveRegistrationCompleteUpdateResult;
}