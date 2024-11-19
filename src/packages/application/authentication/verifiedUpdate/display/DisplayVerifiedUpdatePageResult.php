<?php

namespace packages\domain\model\authConfirmation;

class DisplayVerifiedUpdatePageResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;
    readonly string $oneTimeTokenValue;
}