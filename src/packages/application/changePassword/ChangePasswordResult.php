<?php

namespace packages\application\changePassword;

class ChangePasswordResult
{
    readonly array $validationErrorMessageList;
    readonly bool $isValidationError;
    readonly string $redirectUrl;
}