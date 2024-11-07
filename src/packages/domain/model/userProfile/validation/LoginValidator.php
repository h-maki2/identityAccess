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

        if (!$userProfile->isValid($currentDateTime)) {
            return false;
        }

        if (!$userProfile->password()->equals($inputedPassword)) {
            return false;
        }

        return true;
    }
}