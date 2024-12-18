<?php

namespace packages\application\authentication\verifiedUpdate\update;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;

/**
 * 認証済み更新を行うアプリケーションサービス
 */
class VerifiedUpdateApplicationService implements VerifiedUpdateInputBoundary
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private VerifiedUpdate $verifiedUpdate;
    private AuthConfirmationValidation $authConfirmationValidation;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        TransactionManage $transactionManage
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->verifiedUpdate = new VerifiedUpdate(
            $authenticationAccountRepository,
            $this->authConfirmationRepository,
            $transactionManage
        );
        $this->authConfirmationValidation = new AuthConfirmationValidation($this->authConfirmationRepository);
    }

    /**
     * 認証済み更新を行う
     */
    public function verifiedUpdate(string $oneTimeTokenValueString, string $oneTimePasswordString): VerifiedUpdateResult
    {
        if (!$this->authConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenValueString)) {
            return VerifiedUpdateResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        return VerifiedUpdateResult::createWhenSuccess();
    }
}