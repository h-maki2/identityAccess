<?php

namespace packages\domain\model\authenticationInformation\validation;

use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\common\validator\Validator;
use packages\domain\service\AuthenticationInformation\AuthenticationInformationService;
use PharIo\Manifest\Email;

class UserEmailValidation extends Validator
{
    private string $email;
    private IAuthenticationInformationRepository $authenticationInformationRepository;

    public function __construct(
        string $email, 
        IAuthenticationInformationRepository $authenticationInformationRepository
    )
    {
        $this->email = $email;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
    }

    public function validate(): bool
    {
        if (UserEmailFormatChecker::invalidEmailLength($this->email) || UserEmailFormatChecker::invalidEmail($this->email)) {
            $this->setErrorMessage('不正なメールアドレスです。');
            return false;
        }

        $userEmail = new UserEmail($this->email);
        $authenticationInformationService = new AuthenticationInformationService($this->authenticationInformationRepository);
        if ($authenticationInformationService->alreadyExistsEmail($userEmail)) {
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