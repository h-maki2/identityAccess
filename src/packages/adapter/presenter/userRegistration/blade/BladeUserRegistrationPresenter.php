<?php

namespace packages\adapter\presenter\userRegistration\blade;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;

class BladeUserRegistrationPresenter extends UserRegistrationPresenter
{
    protected function setResponseDataToView(): void
    {
        if (!$this->result->isValidationError) {
            $this->view->setResponseData([]);
            return;
        }


        $this->view->setResponseData(['validationErrors' => $this->result->validationErrors]);
    }

    protected function setHttpStatusToView(): void
    {
        $httpStatus = $this->result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
        $this->view->setHttpStatus($httpStatus);
    }
}