<?php

namespace packages\domain\model\authConfirmation\validation;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;

class AuthConfirmationValidation
{
    private IAuthConfirmationRepository $authConfirmationRepository;

    public function __construct(IAuthConfirmationRepository $authConfirmationRepository)
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
    }

    /**
     * ワンタイムパスワードとワンタイムトークンが有効かどうかを検証する
     */
    public function validate(string $oneTimePasswordString, string $oneTimeToken): bool
    {
        if (!OneTimeTokenValueValidation::validate($oneTimeToken)) {
            return false;
        }

        if (!OneTimePasswordValidation::validate($oneTimePasswordString)) {
            return false;
        }

        $authConfirmation = $this->fetchAuthConfirmation($oneTimeToken);
        if (!$authConfirmation) {
            return false;
        }

        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        
        if ($authConfirmation->isValid($oneTimePassword, new DateTimeImmutable())) {
            return true;
        }

        return false;
    }

    private function fetchAuthConfirmation(string $oneTimeToken): AuthConfirmation
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeToken);
        return $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);
    }
}