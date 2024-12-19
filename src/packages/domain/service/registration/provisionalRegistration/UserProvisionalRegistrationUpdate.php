<?php

namespace packages\domain\service\registration\provisionalRegistration;

use packages\application\common\exception\TransactionException;
use packages\domain\model\email\SendEmailDto;
use packages\domain\service\registration\provisionalRegistration\IUserProvisionalRegistrationCompletionEmail;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\DefinitiveRegistrationConfirmationEmailDtoFactory;
use packages\domain\service\definitiveRegistrationConfirmation\OneTimeTokenExistsService;
use packages\domain\service\authenticationAccount\AuthenticationAccountService;

class UserProvisionalRegistrationUpdate
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private TransactionManage $transactionManage;
    private AuthenticationAccountService $authenticationAccountService;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->transactionManage = $transactionManage;
        $this->authenticationAccountService = new AuthenticationAccountService($authenticationAccountRepository);
        $this->emailSender = $emailSender;
    }

    /**
     * ユーザー登録を行う
     * ユーザー登録後にメールを送信する
     */
    public function handle(UserEmail $email, UserPassword $password, OneTimeToken $oneTimeToken)
    {
        $authAccount = AuthenticationAccount::create(
            $this->authenticationAccountRepository->nextUserId(),
            $email,
            $password,
            $this->authenticationAccountService
        );

        $definitiveRegistrationConfirmation = DefinitiveRegistrationConfirmation::create(
            $authAccount->id(), 
            $oneTimeToken, 
            new OneTimeTokenExistsService($this->definitiveRegistrationConfirmationRepository)
        );

        try {
            $this->transactionManage->performTransaction(function () use ($authAccount, $definitiveRegistrationConfirmation) {
                $this->authenticationAccountRepository->save($authAccount);
                $this->definitiveRegistrationConfirmationRepository->save($definitiveRegistrationConfirmation);
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->emailSender->send(
            DefinitiveRegistrationConfirmationEmailDtoFactory::create(
                $email,
                $definitiveRegistrationConfirmation->oneTimeToken(),
                $definitiveRegistrationConfirmation->oneTimePassword()
            )
        );
    }
}