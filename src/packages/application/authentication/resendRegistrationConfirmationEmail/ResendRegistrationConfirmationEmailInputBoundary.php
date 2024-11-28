<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

interface ResendRegistrationConfirmationEmailInputBoundary
{
    public function resendRegistrationConfirmationEmail(string $email): ResendRegistrationConfirmationEmailOutputBoundary;
}