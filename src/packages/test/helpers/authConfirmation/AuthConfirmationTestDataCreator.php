<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\UserId;

class AuthConfirmationTestDataCreator
{
    private IAuthConfirmationRepository $authConfirmationRepository;

    public function __construct(IAuthConfirmationRepository $authConfirmationRepository)
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
    }

    public function create(
        ?UserId $userId = null,
        ?OneTimeTokenValue $oneTimeTokenValue = null,
        ?OneTimeTokenExpiration $oneTimeTokenExpiration = null,
        ?OneTimePassword $oneTimePassword = null
    ): AuthConfirmation
    {
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken($oneTimeTokenValue, $oneTimeTokenExpiration);
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation($userId, $oneTimeToken, $oneTimePassword);
        $this->authConfirmationRepository->save($authConfirmation);
        return $authConfirmation;
    }
}