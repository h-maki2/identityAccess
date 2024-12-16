<?php

namespace packages\adapter\presenter\userRegistration\blade;

class BladeUserRegistrationViewModel
{
    readonly array $validationErrorList;
    readonly bool $isValidationError;

    public function __construct(
        array $validationErrorList, 
        bool $isValidationError
    )
    {
        $this->validationErrorList = $validationErrorList;
        $this->isValidationError = $isValidationError;
    }
}