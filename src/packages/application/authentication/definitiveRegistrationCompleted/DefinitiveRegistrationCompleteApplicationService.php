<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\definitiveRegistrationConfirmation\validation\definitiveRegistrationConfirmationValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\service\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedUpdate;

/**
 * 本登録済み更新を行うアプリケーションサービス
 */
class DefinitiveRegistrationCompleteApplicationService implements DefinitiveRegistrationCompleteInputBoundary
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private DefinitiveRegistrationCompletedUpdate $DefinitiveRegistrationCompletedUpdate;
    private DefinitiveRegistrationConfirmationValidation $definitiveRegistrationConfirmationValidation;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->DefinitiveRegistrationCompletedUpdate = new DefinitiveRegistrationCompletedUpdate(
            $authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $transactionManage
        );
        $this->definitiveRegistrationConfirmationValidation = new DefinitiveRegistrationConfirmationValidation($this->definitiveRegistrationConfirmationRepository);
    }

    /**
     * 本登録済み更新を行う
     */
    public function handle(string $oneTimeTokenValueString, string $oneTimePasswordString): DefinitiveRegistrationCompleteUpdateResult
    {
        if (!$this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenValueString)) {
            return DefinitiveRegistrationCompleteUpdateResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $this->DefinitiveRegistrationCompletedUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        return DefinitiveRegistrationCompleteUpdateResult::createWhenSuccess();
    }
}