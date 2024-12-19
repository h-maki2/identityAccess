<?php

namespace packages\domain\model\definitiveRegistrationConfirmation\validation;

use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\common\validator\Validator;
use packages\domain\service\registration\definitiveRegistration\OneTimeTokenExistsService;

class OneTimeTokenValidation extends Validator
{
    private OneTimeTokenExistsService $oneTimeTokenExistsService;
    private OneTimeToken $oneTimeToken;

    public function __construct(IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository, OneTimeToken $oneTimeToken)
    {
        $this->oneTimeTokenExistsService = new OneTimeTokenExistsService($definitiveRegistrationConfirmationRepository);
        $this->oneTimeToken = $oneTimeToken;
    }

    public function validate(): bool
    {
        if ($this->oneTimeTokenExistsService->isExists($this->oneTimeToken->tokenValue())) {
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