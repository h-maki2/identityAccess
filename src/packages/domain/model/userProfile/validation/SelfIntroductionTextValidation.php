<?php

namespace packages\domain\model\userProfile\validation;

use packages\domain\model\common\validator\Validator;

class SelfIntroductionTextValidation extends Validator
{
    private const MAX_LENGTH = 500;

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function validate(): bool
    {
        if (!$this->isValidLength()) {
            $this->setErrorMessage('自己紹介文は500文字以内で入力してください。');
            return false;
        }

        return true;
    }

    public function fieldName(): string
    {
        return 'selfIntroductionText';
    }

    private function isValidLength(): bool
    {
        return mb_strlen($this->value, 'UTF-8') <= self::MAX_LENGTH;
    }
}