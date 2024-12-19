<?php

namespace packages\domain\service\registration\definitiveRegistration;

use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;

class OneTimeTokenExistsService
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;

    public function __construct(IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository)
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
    }

    public function isExists(OneTimeTokenValue $oneTimeTokenValue): bool
    {
        return $this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue) !== null;
    }
}