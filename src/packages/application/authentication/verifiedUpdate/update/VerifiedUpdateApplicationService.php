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
class VerifiedUpdateApplicationService
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private VerifiedUpdate $verifiedUpdate;
    private VerifiedUpdateOutputBoundary $outputBoundary;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork,
        VerifiedUpdateOutputBoundary $outputBoundary
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->verifiedUpdate = new VerifiedUpdate(
            $authenticationInformationRepository,
            $this->authConfirmationRepository,
            $unitOfWork
        );
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * 認証済み更新を行う
     */
    public function verifiedUpdate(string $oneTimeTokenValueString, string $oneTimePasswordString): void
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validateExpirationDate($authConfirmation, new DateTimeImmutable())) {
            $this->outputBoundary->formatForResponse(
                VerifiedUpdateResult::createWhenValidationError('ワンタイムトークンが無効です。')
            );
            return;
        }

        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $verifiedUpdateResult = $this->verifiedUpdate->handle($authConfirmation, $oneTimePassword);

        if (!$verifiedUpdateResult) {
            $this->outputBoundary->formatForResponse(
                VerifiedUpdateResult::createWhenValidationError('ワンタイムトークンかワンタイムパスワードが無効です。')
            );
            return;
        }

        $this->outputBoundary->formatForResponse(
            VerifiedUpdateResult::createWhenSuccess()
        );
    }
}