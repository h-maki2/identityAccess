<?php

namespace packages\domain\model\authConfirmation\validation;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\common\validator\Validator;
use packages\domain\service\authConfirmation\AuthConfirmationService;

class OneTimeTokenValidation extends Validator
{
    private AuthConfirmationService $authConfirmationService;
    private OneTimeToken $oneTimeToken;

    public function __construct(IAuthConfirmationRepository $authConfirmationRepository, OneTimeToken $oneTimeToken)
    {
        $this->authConfirmationService = new AuthConfirmationService($authConfirmationRepository);
        $this->oneTimeToken = $oneTimeToken;
    }

    public function validate(): bool
    {
        if ($this->authConfirmationService->isExistsOneTimeToken($this->oneTimeToken->tokenValue())) {
            $this->setErrorMessage('一時的なエラーが発生しました。もう一度お試しください。');
            return false;
        }

        return true;
    }

    public function fieldName(): string
    {
        return 'oneTimeToken';
    }
}