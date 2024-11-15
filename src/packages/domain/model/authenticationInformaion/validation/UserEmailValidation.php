<?php

namespace packages\domain\model\authenticationInformaion\validation;

use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\common\validator\Validator;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;
use PharIo\Manifest\Email;

class UserEmailValidation extends Validator
{
    private string $email;
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(
        string $email, 
        IAuthenticationInformaionRepository $authenticationInformaionRepository
    )
    {
        $this->email = $email;
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
    }

    public function validate(): bool
    {
        if (UserEmailFormatChecker::invalidEmailLength($this->email) || UserEmailFormatChecker::invalidEmail($this->email)) {
            $this->setErrorMessage('不正なメールアドレスです。');
            return false;
        }

        $userEmail = new UserEmail($this->email);
        $authenticationInformaionService = new AuthenticationInformaionService($this->authenticationInformaionRepository);
        if ($authenticationInformaionService->alreadyExistsEmail($userEmail)) {
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