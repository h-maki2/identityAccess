<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

interface ResendDefinitiveRegistrationConfirmationInputBoundary
{
    public function resendRegistrationConfirmationEmail(string $email): ResendDefinitiveRegistrationConfirmationResult;
}