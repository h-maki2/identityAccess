<?php

namespace packages\application\authentication\ResendRegistrationConfirmationEmail;

interface ResendRegistrationConfirmationEmailInputBoundary
{
    public function resendRegistrationConfirmationEmail(string $email): ResendRegistrationConfirmationEmailOutputBoundary;
}