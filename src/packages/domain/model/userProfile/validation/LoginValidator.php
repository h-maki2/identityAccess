<?php

namespace packages\domain\model\userProfile\validation;

use DateTimeImmutable;
use packages\domain\model\userProfile\UserProfile;

class LoginValidator
{
    public static function validate(
        ?UserProfile $userProfile,
        string $inputedPassword,
        DateTimeImmutable $currentDateTime
    ): bool
    {
        if ($userProfile === null) {
            return false;
        }

        if (!$userProfile->isVerified()) {
            return false;
        }

        if ($userProfile->isLocked($currentDateTime)) {
            return false;
        }

        if (!$userProfile->password()->equals($inputedPassword)) {
            return false;
        }

        return true;
    }
}