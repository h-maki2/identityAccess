<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\AuthenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\AuthenticationInformation\UserId;

class AuthConfirmationTestDataCreator
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformationRepository $authenticationInformationRepository;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
    }

    public function create(
        UserId $userId,
        ?OneTimeTokenValue $oneTimeTokenValue = null,
        ?OneTimeTokenExpiration $oneTimeTokenExpiration = null,
        ?OneTimePassword $oneTimePassword = null
    ): AuthConfirmation
    {
        $authenticationInformation = $this->authenticationInformationRepository->findById($userId);
        if ($authenticationInformation === null) {
            throw new \RuntimeException('認証情報テーブルに事前にデータを登録してください。');
        }
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken($oneTimeTokenValue, $oneTimeTokenExpiration);
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation($userId, $oneTimeToken, $oneTimePassword);
        $this->authConfirmationRepository->save($authConfirmation);
        return $authConfirmation;
    }
}