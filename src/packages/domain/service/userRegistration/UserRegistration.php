<?php

namespace packages\domain\service\userRegistration;

use packages\application\common\exception\TransactionException;
use packages\domain\model\email\SendEmailDto;
use packages\domain\service\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\VerifiedUpdateEmailDtoFactory;
use packages\domain\service\authConfirmation\OneTimeTokenExistsService;
use packages\domain\service\authenticationAccount\AuthenticationAccountService;

class UserRegistration
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private TransactionManage $transactionManage;
    private AuthenticationAccountService $authenticationAccountService;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
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

        $authConfirmation = AuthConfirmation::create(
            $authAccount->id(), 
            $oneTimeToken, 
            new OneTimeTokenExistsService($this->authConfirmationRepository)
        );

        try {
            $this->transactionManage->performTransaction(function () use ($authAccount, $authConfirmation) {
                $this->authenticationAccountRepository->save($authAccount);
                $this->authConfirmationRepository->save($authConfirmation);
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->emailSender->send(
            VerifiedUpdateEmailDtoFactory::create(
                $email,
                $authConfirmation->oneTimeToken(),
                $authConfirmation->oneTimePassword()
            )
        );
    }
}