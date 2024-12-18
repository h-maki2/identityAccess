<?php

namespace packages\application\userRegistration;

use Exception;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\validation\OneTimeTokenValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\validation\UserEmailValidation;
use packages\domain\model\authenticationAccount\validation\UserPasswordConfirmationValidation;
use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\service\userRegistration\UserRegistration;
use packages\domain\model\email\IEmailSender;

/**
 * ユーザー登録のアプリケーションサービス
 */
class UserRegistrationApplicationService implements UserRegistrationInputBoundary
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UserRegistration $userRegistration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    )
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->userRegistration = new UserRegistration(
            $authenticationAccountRepository,
            $authConfirmationRepository,
            $transactionManage,
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
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationAccountRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        $validationHandler->addValidator(new UserPasswordConfirmationValidation($inputedPassword, $inputedPasswordConfirmation));

        $oneTimeToken = OneTimeToken::create();
        $validationHandler->addValidator(new OneTimeTokenValidation($this->authConfirmationRepository, $oneTimeToken));
        
        if (!$validationHandler->validate()) {
            return UserRegistrationResult::createWhenValidationError(
                $validationHandler->errorMessages()
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