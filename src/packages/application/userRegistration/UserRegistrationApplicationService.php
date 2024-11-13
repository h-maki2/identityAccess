<?php

namespace packages\application\userRegistration;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;

class UserRegistrationApplicationService
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private AuthenticationInformaionService $authenticationInformaionService;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->authenticationInformaionService = new AuthenticationInformaionService($authenticationInformaionRepository);
    }

    public function userRegister(string $email, string $password)
    {
        
    }
}