<?php

namespace packages\adapter\presenter\userRegistration\blade;

use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\userRegistration\UserRegistrationResult;

class BladeUserRegistrationPresenter extends UserRegistrationPresenter
{
    public function __construct(UserRegistrationResult $result)
    {
        parent::__construct($result);
    }

    public function viewResponse(): BladeUserRegistrationViewModel
    {
        return new BladeUserRegistrationViewModel(
            $this->responseData(), 
            $this->result->validationError
        );
    }
}