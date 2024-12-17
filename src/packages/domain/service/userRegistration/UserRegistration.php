<?php

namespace packages\domain\service\userRegistration;

use packages\domain\model\email\SendEmailDto;
use packages\domain\service\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\VerifiedUpdateEmailDtoFactory;
use packages\domain\service\authConfirmation\OneTimeTokenExistsService;
use packages\domain\service\authenticationInformation\AuthenticationInformationService;

class UserRegistration
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private TransactionManage $transactionManage;
    private AuthenticationInformationService $authenticationInformationService;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    ) {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->transactionManage = $transactionManage;
        $this->authenticationInformationService = new AuthenticationInformationService($authenticationInformationRepository);
        $this->emailSender = $emailSender;
    }

    /**
     * ユーザー登録を行う
     * ユーザー登録後にメールを送信する
     */
    public function handle(UserEmail $email, UserPassword $password, OneTimeToken $oneTimeToken)
    {
        $authInformation = AuthenticationInformation::create(
            $this->authenticationInformationRepository->nextUserId(),
            $email,
            $password,
            $this->authenticationInformationService
        );

        $authConfirmation = AuthConfirmation::create(
            $authInformation->id(), 
            $oneTimeToken, 
            new OneTimeTokenExistsService($this->authConfirmationRepository)
        );

        $this->transactionManage->performTransaction(function () use ($authInformation, $authConfirmation) {
            $this->authenticationInformationRepository->save($authInformation);
            $this->authConfirmationRepository->save($authConfirmation);
        });

        $this->emailSender->send(
            VerifiedUpdateEmailDtoFactory::create(
                $email,
                $authConfirmation->oneTimeToken(),
                $authConfirmation->oneTimePassword()
            )
        );
    }
}