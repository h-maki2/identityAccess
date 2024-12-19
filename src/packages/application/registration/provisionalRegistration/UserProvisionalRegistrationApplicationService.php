<?php

namespace packages\application\registration\userProvisionalRegistration;

use Exception;
use packages\application\common\exception\TransactionException;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\validation\OneTimeTokenValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\validation\UserEmailValidation;
use packages\domain\model\authenticationAccount\validation\UserPasswordConfirmationValidation;
use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\service\registration\provisionalRegistration\UserProvisionalRegistration;
use packages\domain\model\email\IEmailSender;

/**
 * ユーザー登録のアプリケーションサービス
 */
class UserProvisionalRegistrationApplicationService implements UserProvisionalRegistrationInputBoundary
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private UserProvisionalRegistration $userProvisionalRegistration;

    public function __construct(
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    )
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->userProvisionalRegistration = new UserProvisionalRegistration(
            $authenticationAccountRepository,
            $definitiveRegistrationConfirmationRepository,
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
    ): UserProvisionalRegistrationResult
    {
        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationAccountRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        $validationHandler->addValidator(new UserPasswordConfirmationValidation($inputedPassword, $inputedPasswordConfirmation));

        $oneTimeToken = OneTimeToken::create();
        $validationHandler->addValidator(new OneTimeTokenValidation($this->definitiveRegistrationConfirmationRepository, $oneTimeToken));
        
        if (!$validationHandler->validate()) {
            return UserProvisionalRegistrationResult::createWhenValidationError(
                $validationHandler->errorMessages()
            );
        }

        $userEmail = new UserEmail($inputedEmail);
        $userPassword = UserPassword::create($inputedPassword);
        try {
            $this->userProvisionalRegistration->handle($userEmail, $userPassword, $oneTimeToken);
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        return UserProvisionalRegistrationResult::createWhenSuccess();
    }
}