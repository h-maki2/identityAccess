<?php

namespace packages\application\authentication\resendDefinitiveRegistrationConfirmation;

interface ResendDefinitiveRegistrationConfirmationInputBoundary
{
    public function resendRegistrationConfirmationEmail(string $email): ResendDefinitiveRegistrationConfirmationResult;
}