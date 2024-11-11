<?php

namespace packages\domain\model\authenticationInformaion\validation;

use DateTimeImmutable;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;

class LoginValidator
{
    public static function validate(
        AuthenticationInformaion $authenticationInformaion,
        string $inputedPassword,
        DateTimeImmutable $currentDateTime
    ): bool
    {
        if (!$authenticationInformaion->isVerified()) {
            return false;
        }
        
        if ($authenticationInformaion->canReloggedIn($currentDateTime)) {
            return false;
        }
 
        if (!$authenticationInformaion->password()->equals($inputedPassword)) {
            return false;
        }

        return true;
    }
}