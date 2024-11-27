<?php

namespace packages\domain\service\userRegistration;

use packages\domain\model\common\email\SendEmailDto;
use packages\domain\service\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\service\AuthenticationInformation\AuthenticationInformationService;
use packages\service\common\email\IEmailSender;

class UserRegistration
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UnitOfWork $unitOfWork;
    private AuthenticationInformationService $authenticationInformationService;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork,
        IEmailSender $emailSender
    ) {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->unitOfWork = $unitOfWork;
        $this->authenticationInformationService = new AuthenticationInformationService($authenticationInformationRepository);
        $this->emailSender = $emailSender;
    }

    /**
     * ユーザー登録を行う
     * ユーザー登録後にメールを送信する
     */
    public function handle(UserEmail $email, UserPassword $password)
    {
        $authInformation = AuthenticationInformation::create(
            $this->authenticationInformationRepository->nextUserId(),
            $email,
            $password,
            $this->authenticationInformationService
        );

        $authConfirmation = AuthConfirmation::create($authInformation->id());

        $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
            $this->authenticationInformationRepository->save($authInformation);
            $this->authConfirmationRepository->save($authConfirmation);
        });

        $this->emailSender->send(
            $this->createUserRegistrationCompletionEmailDto(
                $email->value,
                $authConfirmation->oneTimeToken()->value(),
                $authConfirmation->oneTimePassword()->value
            )
        );
    }

    private function createUserRegistrationCompletionEmailDto(
        string $toAddress,
        string $oneTimeToken,
        string $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'oneTimeToken' => $oneTimeToken,
            'oneTimePassword' => $oneTimePassword
        ];
        return new SendEmailDto(
            'test@example.com',
            $toAddress,
            'システムテスト',
            '会員登録完了のお知らせ',
            'email.test',
            $templateValiables
        );
    }
}