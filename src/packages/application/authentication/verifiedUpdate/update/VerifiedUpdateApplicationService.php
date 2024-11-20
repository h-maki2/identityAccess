<?php

namespace packages\application\authentication\verifiedUpdate\update;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;

/**
 * 認証済み更新を行うアプリケーションサービス
 */
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

    /**
     * 認証済み更新を行う
     */
    public function verifiedUpdate(string $oneTimeTokenValueString, string $oneTimePasswordString)
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByToken($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validate($authConfirmation, new DateTimeImmutable())) {
            return;
        }

        
        if ($authConfirmation->oneTimePassword()->equals())
    }
}