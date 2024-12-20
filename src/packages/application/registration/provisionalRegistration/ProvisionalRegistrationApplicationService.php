<?php

namespace packages\application\registration\provisionalRegistration;

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
use packages\domain\model\email\IEmailSender;
use packages\domain\service\registration\provisionalRegistration\ProvisionalRegistrationUpdate;

/**
 * ユーザー登録のアプリケーションサービス
 */
class ProvisionalRegistrationApplicationService implements ProvisionalRegistrationInputBoundary
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private ProvisionalRegistrationUpdate $provisionalRegistrationUpdate;

    public function __construct(
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository,
        TransactionManage $transactionManage,
        IEmailSender $emailSender
    )
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->provisionalRegistrationUpdate = new ProvisionalRegistrationUpdate(
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
    ): ProvisionalRegistrationResult
    {
        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserEmailValidation($inputedEmail, $this->authenticationAccountRepository));
        $validationHandler->addValidator(new UserPasswordValidation($inputedPassword));
        $validationHandler->addValidator(new UserPasswordConfirmationValidation($inputedPassword, $inputedPasswordConfirmation));

        $oneTimeToken = OneTimeToken::create();
        $validationHandler->addValidator(new OneTimeTokenValidation($this->definitiveRegistrationConfirmationRepository, $oneTimeToken));
        
        if (!$validationHandler->validate()) {
            return ProvisionalRegistrationResult::createWhenValidationError(
                $validationHandler->errorMessages()
            );
        }

        $userEmail = new UserEmail($inputedEmail);
        $userPassword = UserPassword::create($inputedPassword);
        try {
            $this->provisionalRegistrationUpdate->handle($userEmail, $userPassword, $oneTimeToken);
        } catch (Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        return ProvisionalRegistrationResult::createWhenSuccess();
    }
}