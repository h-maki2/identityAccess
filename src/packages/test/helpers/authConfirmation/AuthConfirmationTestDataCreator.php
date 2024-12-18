<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserId;

class AuthConfirmationTestDataCreator
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    public function create(
        UserId $userId,
        ?OneTimeTokenValue $oneTimeTokenValue = null,
        ?OneTimeTokenExpiration $oneTimeTokenExpiration = null,
        ?OneTimePassword $oneTimePassword = null
    ): AuthConfirmation
    {
        $authenticationAccount = $this->authenticationAccountRepository->findById($userId);
        if ($authenticationAccount === null) {
            throw new \RuntimeException('認証情報テーブルに事前にデータを登録してください。');
        }
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken($oneTimeTokenValue, $oneTimeTokenExpiration);
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation($userId, $oneTimeToken, $oneTimePassword);
        $this->authConfirmationRepository->save($authConfirmation);
        return $authConfirmation;
    }
}