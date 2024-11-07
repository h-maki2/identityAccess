<?php

namespace packages\domain\model\userProfile\validation;

use DateTimeImmutable;
use packages\domain\model\userProfile\UserProfile;

class LoginValidator
{
    /**
     * ログイン可能かどうかを判定する
     */
    public function validate(
        UserProfile $userProfile, 
        string $inputedPassword, 
        DateTimeImmutable $currentDateTime
    ): bool
    {
        if (!$userProfile->isVerified()) {
            return false;
        }

        if (!$userProfile->canLogin($currentDateTime)) {
            return false;
        }

        if (!$userProfile->password()->equals($inputedPassword)) {
            return false;
        }

        return true;
    }
}