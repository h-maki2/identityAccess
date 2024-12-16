<?php

namespace packages\adapter\presenter\userRegistration\blade;

class BladeUserRegistrationViewModel
{
    readonly array $validationErrorList;
    readonly bool $isValidationError;
    readonly string $email;
    readonly string $password;
    readonly string $passwordConfirmation;

    public function __construct(
        array $validationErrorList, 
        bool $isValidationError,
        string $email,
        string $password,
        string $passwordConfirmation
    )
    {
        $this->validationErrorList = $validationErrorList;
        $this->isValidationError = $isValidationError;
        $this->email = $email;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }
}