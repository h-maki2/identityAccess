<?php

namespace packages\domain\model\authenticationInformaion\validation;

use DateTimeImmutable;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;

class LoginAvailabilityService
{
    public static function isLoginAvailable(
        AuthenticationInformaion $authenticationInformaion,
        string $inputedPassword, 
        DateTimeImmutable $currentDateTime
    ): bool
    {
        if (!$authenticationInformaion->canLoggedIn($currentDateTime)) {
            return false;
        }

        if ($authenticationInformaion->password()->equals($inputedPassword)) {
            return true;
        }

        return false;
    }
}