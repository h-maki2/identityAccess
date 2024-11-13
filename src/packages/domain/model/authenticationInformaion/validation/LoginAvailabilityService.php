<?php

namespace packages\domain\model\authenticationInformaion\validation;

use DateTimeImmutable;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;

class LoginAvailabilityService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
    }

    public function isLoginAvailable(
        UserEmail $email, 
        string $inputedPassword, 
        DateTimeImmutable $currentDateTime
    ): bool
    {
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);

        if ($authenticationInformaion === null) {
            return false;
        }

        if (!$authenticationInformaion->canLoggedIn($currentDateTime)) {
            return false;
        }

        if ($authenticationInformaion->password()->equals($inputedPassword)) {
            return true;
        }

        return false;
    }
}