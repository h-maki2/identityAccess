<?php

namespace packages\application\registration\resendDefinitiveRegistrationConfirmation;

interface ResendDefinitiveRegistrationConfirmationInputBoundary
{
    public function handle(string $email): ResendDefinitiveRegistrationConfirmationResult;
}