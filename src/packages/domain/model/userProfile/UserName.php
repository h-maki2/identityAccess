<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\userProfile\validation\UserNameFormatChecker;

class UserName
{
    readonly string $value;

    public function __construct(string $value) {

        if (UserNameFormatChecker::invalidUserNameLength($value)) {
            throw new InvalidArgumentException('ユーザー名が無効です。');
        }

        if (UserNameFormatChecker::onlyWhiteSpace($value)) {
            throw new InvalidArgumentException('ユーザー名が空です。');
        }

        $this->value = $value;        
    }
}