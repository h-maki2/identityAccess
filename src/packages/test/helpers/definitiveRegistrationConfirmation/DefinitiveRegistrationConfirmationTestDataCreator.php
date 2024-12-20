<?php

namespace packages\test\helpers\definitiveRegistrationConfirmation;

use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserId;

class DefinitiveRegistrationConfirmationTestDataCreator
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    public function create(
        UserId $userId,
        ?OneTimeTokenValue $oneTimeTokenValue = null,
        ?OneTimeTokenExpiration $oneTimeTokenExpiration = null,
        ?OneTimePassword $oneTimePassword = null
    ): DefinitiveRegistrationConfirmation
    {
        $authenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        if ($authenticationAccount === null) {
            throw new \RuntimeException('認証アカウントを事前に作成してください。');
        }
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken($oneTimeTokenValue, $oneTimeTokenExpiration);
        $definitiveRegistrationConfirmation = TestDefinitiveRegistrationConfirmationFactory::create($userId, $oneTimeToken, $oneTimePassword);
        $this->definitiveRegistrationConfirmationRepository->save($definitiveRegistrationConfirmation);
        return $definitiveRegistrationConfirmation;
    }
}