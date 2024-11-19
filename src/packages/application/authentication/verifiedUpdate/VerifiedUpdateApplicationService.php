<?php

namespace packages\application\authentication\verifiedUpdate;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;

class VerifiedUpdateApplicationService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        IAuthConfirmationRepository $authConfirmationRepository
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
    }

    public function verifiedUpdate()
    {
        
    }
}