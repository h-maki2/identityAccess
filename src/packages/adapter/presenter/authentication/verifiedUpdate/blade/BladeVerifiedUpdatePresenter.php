<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\blade;

use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;

class BladeVerifiedUpdatePresenter
{
    private VerifiedUpdateResult $verifiedUpdateResult;

    public function __construct(VerifiedUpdateResult $verifiedUpdateResult)
    {
        $this->verifiedUpdateResult = $verifiedUpdateResult;
    }

    public function responseDate(): array
    {
        if ($this->verifiedUpdateResult->validationError) {
            return [
                'validationErrorMessage' => $this->verifiedUpdateResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->verifiedUpdateResult->validationError;
    }
}