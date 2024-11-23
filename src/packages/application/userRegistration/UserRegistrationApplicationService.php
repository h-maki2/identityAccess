<?php

namespace packages\domain\service\userRegistration;

use Exception;
use packages\domain\model\common\email\SendEmailDto;
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
    private UserRegistration $userRegistration;
    private UserRegistrationOutputBoundary $outputBoundary;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        UnitOfWork $unitOfWork,
        IUserRegistrationCompletionEmail $userRegistrationCompletionEmail,
        UserRegistrationOutputBoundary $outputBoundary
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->userRegistration = new UserRegistration(
            $authenticationInformaionRepository,
            $authConfirmationRepository,
            $unitOfWork,
            $userRegistrationCompletionEmail
        );
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * ユーザー登録を行う
     */
    public function userRegister(
        string $inputedEmail, 
        string $inputedPassword
    ): void
    {
        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationInformaionRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        if (!$validationHandler->validate()) {
            $this->outputBoundary->present(
                UserRegistrationResult::createWhenValidationError($validationHandler->errorMessages())
            );
            return;
        }

        $userEmail = new UserEmail($inputedEmail);
        $userPassword = UserPassword::create($inputedPassword);
        try {
            $this->userRegistration->handle($userEmail, $userPassword);
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->outputBoundary->present(
            UserRegistrationResult::createWhenSuccess()
        );
        return;
    }
}