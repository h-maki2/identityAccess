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
    }

    /**
     * 認証済み更新を行う
     */
    public function verifiedUpdate(string $oneTimeTokenValueString, string $oneTimePasswordString): VerifiedUpdateResult
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validate($authConfirmation, new DateTimeImmutable())) {
            return VerifiedUpdateResult::createWhenValidationError('ワンタイムトークンが無効です。');
        }

        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $verifiedUpdateResult = $this->verifiedUpdate->handle($authConfirmation, $oneTimePassword);

        if (!$verifiedUpdateResult) {
            return VerifiedUpdateResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。');
        }

        return VerifiedUpdateResult::createWhenSuccess();
    }
}