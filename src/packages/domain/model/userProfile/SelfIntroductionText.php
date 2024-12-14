<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\userProfile\validation\SelfIntroductionTextValidation;

class SelfIntroductionText
{
    readonly string $value;

    public function __construct(string $value)
    {
        $validation = new SelfIntroductionTextValidation($value);

        if (!$validation->validate()) {
            throw new InvalidArgumentException('無効な自己紹介文です。');
        }
        $this->value = $value;
    }
}