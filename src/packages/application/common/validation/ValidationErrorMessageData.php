<?php

namespace packages\application\common\validation;

class ValidationErrorMessageData
{
    readonly string $fieldName;
    readonly array $errorMessageList;

    public function __construct(
        string $fieldName,
        array $errorMessageList
    )
    {
        $this->fieldName = $fieldName;
        $this->errorMessageList = $errorMessageList;
    }
}