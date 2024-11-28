<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

interface ResendRegistrationConfirmationEmailOutputBoundary
{
    public function formatForResponse(ResendRegistrationConfirmationEmailResult $ResendRegistrationConfirmationEmailResult): void;
    public function response(): mixed;
}