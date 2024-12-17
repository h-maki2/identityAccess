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
    public function validate(string $oneTimePasswordString, string $oneTimeTokenString): bool
    {
        if (!OneTimeTokenValueValidation::validate($oneTimeTokenString)) {
            return false;
        }

        if (!OneTimePasswordValidation::validate($oneTimePasswordString)) {
            return false;
        }

        $authConfirmation = $this->fetchAuthConfirmation($oneTimeTokenString);
        if ($authConfirmation === null) {
            return false;
        }

        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        
        if ($authConfirmation->canUpdateVerifiedAuthInfo($oneTimePassword, new DateTimeImmutable())) {
            return true;
        }

        return false;
    }

    private function fetchAuthConfirmation(string $oneTimeTokenString): ?AuthConfirmation
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenString);
        return $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);
    }
}