<?php

namespace packages\application\userRegistration;

use Exception;
use packages\application\common\email\SendEmailDto;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\validation\UserEmailValidation;
use packages\domain\model\authenticationInformaion\validation\UserPasswordValidation;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;

/**
 * ユーザー登録のアプリケーションサービス
 */
class UserRegistrationApplicationService
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private AuthenticationInformaionService $authenticationInformaionService;
    private UnitOfWork $unitOfWork;
    private IUserRegistrationCompletionEmail $userRegistrationCompletionEmail;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        UnitOfWork $unitOfWork,
        IUserRegistrationCompletionEmail $userRegistrationCompletionEmail
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->unitOfWork = $unitOfWork;
        $this->authenticationInformaionService = new AuthenticationInformaionService($authenticationInformaionRepository);
        $this->userRegistrationCompletionEmail = $userRegistrationCompletionEmail;
    }

    /**
     * ユーザー登録を行う
     */
    public function userRegister(
        string $inputedEmail, 
        string $inputedPassword
    ): UserRegistrationResult
    {
        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationInformaionRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        if (!$validationHandler->validate()) {
            return UserRegistrationResult::createWhenValidationError($validationHandler->errorMessages());
        }

        $userEmail = new UserEmail($inputedEmail);
        $userPassword = UserPassword::create($inputedPassword);
        $authInformation = AuthenticationInformaion::create(
            $this->authenticationInformaionRepository->nextUserId(),
            $userEmail,
            $userPassword,
            $this->authenticationInformaionService
        );

        $authConfirmation = AuthConfirmation::create($authInformation->id());

        try {
            $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
                $this->authenticationInformaionRepository->save($authInformation);
                $this->authConfirmationRepository->save($authConfirmation);
            });
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->userRegistrationCompletionEmail->send(
            $this->sendEmailDto(
                $authInformation->email()->value,
                $authConfirmation->oneTimeToken()->value,
                $authConfirmation->oneTimePassword()->value
            )
        );

        return UserRegistrationResult::createWhenSuccess($authConfirmation->oneTimeToken()->value);
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