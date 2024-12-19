<?php

namespace packages\domain\model\authenticationAccount\validation;

use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\common\validator\Validator;
use packages\domain\service\authenticationAccount\AuthenticationAccountService;
use PharIo\Manifest\Email;

class UserEmailValidation extends Validator
{
    private string $email;
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(
        string $email, 
        IAuthenticationAccountRepository $authenticationAccountRepository
    )
    {
        $this->email = $email;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    public function validate(): bool
    {
        $emailChecker = new UserEmailFormatChecker();
        if (!$emailChecker->validate($this->email)) {
            $this->setErrorMessage('不正なメールアドレスです。');
            return false;
        }

        $userEmail = new UserEmail($this->email);
        $authenticationAccountService = new AuthenticationAccountService($this->authenticationAccountRepository);
        if ($authenticationAccountService->alreadyExistsEmail($userEmail)) {
            $this->setErrorMessage('既に登録されているメールアドレスです。');
            return false;
        }

        return true;
    }

    public function fieldName(): string
    {
        return 'email';
    }
}