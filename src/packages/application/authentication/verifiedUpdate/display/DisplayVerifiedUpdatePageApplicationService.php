<?php

namespace packages\application\authentication\verifiedUpdate\display;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;

/**
 * 認証済み更新ページを表示するアプリケーションサービス
 */
class DisplayVerifiedUpdatePageApplicationService implements DisplayVerifiedUpdatePageInputBoundary
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private DisplayVerifiedUpdatePageOutputBoundary $outputBoundary;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        DisplayVerifiedUpdatePageOutputBoundary $outputBoundary
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * 認証済み更新ページを表示する
     */
    public function displayVerifiedUpdatePage(string $oneTimeTokenValueString): DisplayVerifiedUpdatePageOutputBoundary
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validateExpirationDate($authConfirmation, new DateTimeImmutable())) {
            $this->outputBoundary->formatForResponse(
                DisplayVerifiedUpdatePageResult::createWhenValidationError('無効なワンタイムトークンです。')
            );
            return $this->outputBoundary;
        }

        $this->outputBoundary->formatForResponse(
            DisplayVerifiedUpdatePageResult::createWhenSuccess($oneTimeTokenValue)
        );
        return $this->outputBoundary;
    }
}