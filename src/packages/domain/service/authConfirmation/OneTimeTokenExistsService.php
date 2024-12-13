<?php

namespace packages\domain\service\authConfirmation;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeTokenValue;

class OneTimeTokenExistsService
{
    private IAuthConfirmationRepository $authConfirmationRepository;

    public function __construct(IAuthConfirmationRepository $authConfirmationRepository)
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
    }

    public function isExists(OneTimeTokenValue $oneTimeTokenValue): bool
    {
        return $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue) !== null;
    }
}