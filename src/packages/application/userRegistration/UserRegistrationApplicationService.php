<?php

namespace packages\application\userRegistration;

use Exception;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\validation\OneTimeTokenValidation;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\validation\UserEmailValidation;
use packages\domain\model\authenticationInformation\validation\UserPasswordConfirmationValidation;
use packages\domain\model\authenticationInformation\validation\UserPasswordValidation;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\service\userRegistration\UserRegistration;
use packages\domain\model\email\IEmailSender;

/**
 * ユーザー登録のアプリケーションサービス
 */
class UserRegistrationApplicationService implements UserRegistrationInputBoundary
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UserRegistration $userRegistration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository,
        UnitOfWork $unitOfWork,
        IEmailSender $emailSender
    )
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->userRegistration = new UserRegistration(
            $authenticationInformationRepository,
            $authConfirmationRepository,
            $unitOfWork,
            $emailSender
        );
    }

    /**
     * ユーザー登録を行う
     */
    public function userRegister(
        string $inputedEmail, 
        string $inputedPassword,
        string $inputedPasswordConfirmation
    ): UserRegistrationResult
    {
        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationInformationRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        $validationHandler->addValidator(new UserPasswordConfirmationValidation($inputedPassword, $inputedPasswordConfirmation));

        $oneTimeToken = OneTimeToken::create();
        $validationHandler->addValidator(new OneTimeTokenValidation($this->authConfirmationRepository, $oneTimeToken));
        
        if (!$validationHandler->validate()) {
            return UserRegistrationResult::createWhenValidationError(
                $validationHandler->errorMessages(),
                $inputedEmail,
                $inputedPassword,
                $inputedPasswordConfirmation
            );
        }

        $userEmail = new UserEmail($inputedEmail);
        $userPassword = UserPassword::create($inputedPassword);
        try {
            $this->userRegistration->handle($userEmail, $userPassword, $oneTimeToken);
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        return UserRegistrationResult::createWhenSuccess();
    }
}