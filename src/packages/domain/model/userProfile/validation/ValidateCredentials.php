<?php

namespace packages\domain\model\userProfile\validation;

use packages\domain\model\userProfile\UserProfile;

class CredentialsValidation
{
    public function handle(UserProfile $userProfile, string $inputedPassword): bool
    {
        if (!$userProfile->isVerified()) {
            return false;
        }
    }
}