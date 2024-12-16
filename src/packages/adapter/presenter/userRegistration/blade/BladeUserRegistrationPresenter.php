<?php

namespace packages\adapter\presenter\userRegistration\blade;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;

class BladeUserRegistrationPresenter extends UserRegistrationPresenter
{
    public function responseView(): mixed
    {
        return $this->view->response();
    }

    private function setResponseDataToView(): void
    {
        if (!$this->result->isValidationError) {
            $this->view->setResponseData([]);
            return;
        }

        $this->view->setResponseData($this->result->validationErrors);
    }

    private function setHttpStatusToView(): void
    {
        $httpStatus = $this->result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
        $this->view->setHttpStatus($httpStatus);
    }
}