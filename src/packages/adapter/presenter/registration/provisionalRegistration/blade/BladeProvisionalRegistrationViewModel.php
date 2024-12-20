<?php

namespace packages\adapter\presenter\registration\provisionalRegistration\blade;

class BladeProvisionalRegistrationViewModel
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