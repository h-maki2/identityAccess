<?php

namespace packages\application\authentication\ResendRegistrationConfirmationEmail;

interface ResendRegistrationConfirmationEmailOutputBoundary
{
    public function formatForResponse(ResendRegistrationConfirmationEmailResult $ResendRegistrationConfirmationEmailResult): void;
    public function response(): mixed;
}