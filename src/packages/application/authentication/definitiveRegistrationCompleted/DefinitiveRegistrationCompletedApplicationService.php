<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\definitiveRegistrationConfirmation\validation\definitiveRegistrationConfirmationValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\service\definitiveRegistrationCompleted\definitiveRegistrationCompleted;

/**
 * 確認済み更新を行うアプリケーションサービス
 */
class DefinitiveRegistrationCompletedApplicationService implements DefinitiveRegistrationCompletedInputBoundary
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private DefinitiveRegistrationCompleted $DefinitiveRegistrationCompleted;
    private DefinitiveRegistrationConfirmationValidation $definitiveRegistrationConfirmationValidation;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->DefinitiveRegistrationCompleted = new DefinitiveRegistrationCompleted(
            $authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $transactionManage
        );
        $this->definitiveRegistrationConfirmationValidation = new DefinitiveRegistrationConfirmationValidation($this->definitiveRegistrationConfirmationRepository);
    }

    /**
     * 確認済み更新を行う
     */
    public function DefinitiveRegistrationCompleted(string $oneTimeTokenValueString, string $oneTimePasswordString): DefinitiveRegistrationCompletedResult
    {
        if (!$this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenValueString)) {
            return DefinitiveRegistrationCompletedResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $this->DefinitiveRegistrationCompleted->handle($oneTimeTokenValue, $oneTimePassword);

        return DefinitiveRegistrationCompletedResult::createWhenSuccess();
    }
}