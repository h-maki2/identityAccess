<?php

namespace packages\application\authentication\verifiedUpdate\update;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\common\unitOfWork\UnitOfWork;
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
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->verifiedUpdate = new VerifiedUpdate(
            $authenticationInformationRepository,
            $this->authConfirmationRepository,
            $unitOfWork
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