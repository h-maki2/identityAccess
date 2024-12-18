<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\definitiveRegistrationConfirmation\validation\definitiveRegistrationConfirmationValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\service\definitiveRegistrationCompleted\DefinitiveRegistrationConfirmedUpdate;

/**
 * 本登録済み更新を行うアプリケーションサービス
 */
class DefinitiveRegistrationCompletedApplicationServicee implements DefinitiveRegistrationConfirmedUpdateInputBoundary
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private DefinitiveRegistrationConfirmedUpdate $definitiveRegistrationConfirmedUpdate;
    private DefinitiveRegistrationConfirmationValidation $definitiveRegistrationConfirmationValidation;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->definitiveRegistrationConfirmedUpdate = new DefinitiveRegistrationConfirmedUpdate(
            $authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $transactionManage
        );
        $this->definitiveRegistrationConfirmationValidation = new DefinitiveRegistrationConfirmationValidation($this->definitiveRegistrationConfirmationRepository);
    }

    /**
     * 本登録済み更新を行う
     */
    public function DefinitiveRegistrationConfirmedUpdate(string $oneTimeTokenValueString, string $oneTimePasswordString): DefinitiveRegistrationConfirmedUpdateResult
    {
        if (!$this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenValueString)) {
            return DefinitiveRegistrationConfirmedUpdateResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $this->definitiveRegistrationConfirmedUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        return DefinitiveRegistrationConfirmedUpdateResult::createWhenSuccess();
    }
}