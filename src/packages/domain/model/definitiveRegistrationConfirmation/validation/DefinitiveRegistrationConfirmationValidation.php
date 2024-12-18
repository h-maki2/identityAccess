<?php

namespace packages\domain\model\definitiveRegistrationConfirmation\validation;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;

class DefinitiveRegistrationConfirmationValidation
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;

    public function __construct(IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository)
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
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

        $definitiveRegistrationConfirmation = $this->fetchDefinitiveRegistrationConfirmation($oneTimeTokenString);
        if ($definitiveRegistrationConfirmation === null) {
            return false;
        }

        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        
        if ($definitiveRegistrationConfirmation->canUpdatConfirmed($oneTimePassword, new DateTimeImmutable())) {
            return true;
        }

        return false;
    }

    private function fetchDefinitiveRegistrationConfirmation(string $oneTimeTokenString): ?DefinitiveRegistrationConfirmation
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenString);
        return $this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue);
    }
}