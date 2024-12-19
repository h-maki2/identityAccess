<?php

namespace packages\adapter\presenter\UserProvisionalRegistration\blade;

class BladeUserProvisionalRegistrationViewModel
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