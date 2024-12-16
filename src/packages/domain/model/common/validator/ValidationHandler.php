<?php

namespace packages\domain\model\common\validator;

abstract class ValidationHandler
{
    private array $validatorList = []; // Validator[]

    abstract public function validate(): bool;

    abstract public function getValidationError(): mixed;

    public function addValidator(Validator $validator): void
    {
        $this->validatorList[$validator->fieldName()] = $validator;
    }

    /**
     * @return Validator[]
     */
    protected function validatorList(): array
    {
        return $this->validatorList;
    }
}