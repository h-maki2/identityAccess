<?php

namespace packages\application\registration\definitiveRegistration;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\definitiveRegistrationConfirmation\validation\DefinitiveRegistrationConfirmationValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\service\registration\definitiveRegistration\DefinitiveRegistrationUpdate;

/**
 * 本登録済み更新を行うアプリケーションサービス
 */
class DefinitiveRegistrationApplicationService implements DefinitiveRegistrationInputBoundary
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private DefinitiveRegistrationUpdate $definitiveRegistrationUpdate;
    private DefinitiveRegistrationConfirmationValidation $definitiveRegistrationConfirmationValidation;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->definitiveRegistrationUpdate = new DefinitiveRegistrationUpdate(
            $authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $transactionManage
        );
        $this->definitiveRegistrationConfirmationValidation = new DefinitiveRegistrationConfirmationValidation($this->definitiveRegistrationConfirmationRepository);
    }

    /**
     * 本登録済み更新を行う
     */
    public function handle(string $oneTimeTokenValueString, string $oneTimePasswordString): DefinitiveRegistrationResult
    {
        if (!$this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenValueString)) {
            return DefinitiveRegistrationResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $this->definitiveRegistrationUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        return DefinitiveRegistrationResult::createWhenSuccess();
    }
}