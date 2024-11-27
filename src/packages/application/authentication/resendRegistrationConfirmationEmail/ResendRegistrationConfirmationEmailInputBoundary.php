<?php

namespace packages\application\authentication\ResendRegistrationConfirmationEmail;

interface ResendRegistrationConfirmationEmailInputBoundary
{
    public function regenerateOneTimeTokenAndPassword(string $email): ResendRegistrationConfirmationEmailOutputBoundary;
}