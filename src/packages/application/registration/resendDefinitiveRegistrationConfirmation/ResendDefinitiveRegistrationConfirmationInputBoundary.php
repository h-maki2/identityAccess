<?php

namespace packages\application\authentication\resendDefinitiveRegistrationConfirmation;

interface ResendDefinitiveRegistrationConfirmationInputBoundary
{
    public function handle(string $email): ResendDefinitiveRegistrationConfirmationResult;
}