<?php

namespace packages\adapter\presenter\userRegistration\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\application\userRegistration\UserRegistrationResult;
use UserRegistrationView;

class JsonUserRegistrationPresenter extends UserRegistrationPresenter
{
    public function responseView(): JsonResponseData
    {
        $this->setHttpStatusToView();
        $this->setResponseDataToView();
        return $this->jsonResponse();
    }

    private function setResponseDataToView(): void
    {
        if (!$this->result->isValidationError) {
            $this->view->setResponseData([]);
            return;
        }

        $responseData = [];
        foreach ($this->validationErrorMessageList() as $validationError) {
            $responseData[$validationError->fieldName] = $validationError->errorMessageList;
        }
        $this->view->setResponseData($responseData);
    }

    private function setHttpStatusToView(): void
    {
        $httpStatus = $this->result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
        $this->view->setHttpStatus($httpStatus);
    }

    /**
     * @return ValidationErrorMessageData[]
     */
    private function validationErrorMessageList(): array
    {
        return $this->result->validationErrors;
    }

    private function jsonResponse(): JsonResponseData
    {
        return $this->view->response();
    }
}