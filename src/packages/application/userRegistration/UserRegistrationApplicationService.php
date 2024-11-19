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
use packages\domain\service\userRegistration\UserRegistration;

/**
 * ユーザー登録のアプリケーションサービス
 */
class UserRegistrationApplicationService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IUserRegistrationCompletionEmail $userRegistrationCompletionEmail;
    private UserRegistration $userRegistration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        UnitOfWork $unitOfWork,
        IUserRegistrationCompletionEmail $userRegistrationCompletionEmail
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->userRegistrationCompletionEmail = $userRegistrationCompletionEmail;
        $this->userRegistration = new UserRegistration(
            $authenticationInformaionRepository,
            $authConfirmationRepository,
            $unitOfWork
        );
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
        try {
            $authConfirmation = $this->userRegistration->handle($userEmail, $userPassword);
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->userRegistrationCompletionEmail->send(
            $this->sendEmailDto(
                $userEmail->value,
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