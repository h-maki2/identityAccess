<?php

namespace packages\domain\model\userProfile\validation;

class UserNameFormatChecker
{
    private const MAX_USERNAME_LENGTH = 50;
    
    public static function invalidUserNameLength(string $userName): bool
    {
        $userNameLength = mb_strlen($userName, 'UTF-8');
        return $userNameLength === 0 || $userNameLength > self::MAX_USERNAME_LENGTH;
    }

    /**
     * 空白文字列のみかどうかを判定
     * 空白文字列のみだったらtrue
     */
    public static function onlyWhiteSpace(string $userName): bool
    {
        return preg_match('/^\s*$/u', $userName);
    }
}