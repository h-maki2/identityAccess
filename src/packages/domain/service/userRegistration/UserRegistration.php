<?php

namespace packages\domain\service\userRegistration;

use packages\application\common\email\SendEmailDto;
use packages\application\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;

class UserRegistration
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UnitOfWork $unitOfWork;
    private AuthenticationInformaionService $authenticationInformaionService;
    private IUserRegistrationCompletionEmail $userRegistrationCompletionEmail;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork,
        IUserRegistrationCompletionEmail $userRegistrationCompletionEmail
    ) {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->unitOfWork = $unitOfWork;
        $this->authenticationInformaionService = new AuthenticationInformaionService($authenticationInformaionRepository);
        $this->userRegistrationCompletionEmail = $userRegistrationCompletionEmail;
    }

    /**
     * ユーザー登録を行う
     * ユーザー登録後にメールを送信する
     */
    public function handle(UserEmail $email, UserPassword $password)
    {
        $authInformation = AuthenticationInformaion::create(
            $this->authenticationInformaionRepository->nextUserId(),
            $email,
            $password,
            $this->authenticationInformaionService
        );

        $authConfirmation = AuthConfirmation::create($authInformation->id());

        $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
            $this->authenticationInformaionRepository->save($authInformation);
            $this->authConfirmationRepository->save($authConfirmation);
        });

        $this->userRegistrationCompletionEmail->send(
            $this->sendEmailDto(
                $email->value,
                $authConfirmation->oneTimeToken()->value(),
                $authConfirmation->oneTimePassword()->value
            )
        );
    }

    private function sendEmailDto(
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