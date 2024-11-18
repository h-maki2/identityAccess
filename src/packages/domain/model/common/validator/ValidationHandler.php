<?php

namespace packages\domain\model\common\validator;

use packages\application\common\validation\ValidationErrorMessageData;

class ValidationHandler
{
    private array $validatorList = [];
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
    public function errorMessages(): array
    {
        return $this->errorMessages;
    }

    public function addValidator(Validator $validator): void
    {
        $this->validatorList[] = $validator;
    }

    /**
     * @return Validator[]
     */
    private function validatorList(): array
    {
        return $this->validatorList;
    }

    private function setErrorMessage(Validator $validator): void
    {
        $this->errorMessages[] = new ValidationErrorMessageData(
            $validator->fieldName(),
            $validator->errorMessageList()
        );
    }
}