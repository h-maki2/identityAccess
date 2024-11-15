<?php

namespace packages\domain\model\common\validator;

class ValidationHandler
{
    private array $validatorList = [];
    private array $errorMessages = [];

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

    public function setErrorMessage(Validator $validator): void
    {
        $this->errorMessages[] = $validator->errorMessageList();
    }

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
}