<?php

namespace packages\adapter\validator;

use packages\application\common\validation\ValidationErrorMessageData;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\model\common\validator\Validator;

class JsonValidationHandler extends ValidationHandler
{
    private array $errorMessages = []; // ValidationErrorMessageData[]

    public function validate(): bool
    {
        $valid = true;
        foreach ($this->validatorList() as $validator) {
            if (!$validator->validate()) {
                $this->setErrorMessage($validator);
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @return ValidationErrorMessageData[]
     */
    public function getValidationError(): array
    {
        return $this->errorMessages;
    }

    private function setErrorMessage(Validator $validator): void
    {
        $this->errorMessages[] = new ValidationErrorMessageData(
            $validator->fieldName(),
            $validator->errorMessageList()
        );
    }
}