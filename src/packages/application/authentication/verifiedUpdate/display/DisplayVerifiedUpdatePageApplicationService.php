<?php

namespace packages\application\authentication\verifiedUpdate\display;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;

/**
 * 認証済み更新ページを表示するアプリケーションサービス
 */
class DisplayVerifiedUpdatePageApplicationService
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
    public function displayVerifiedUpdatePage(string $oneTimeTokenValueString): void
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByToken($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validateExpirationDate($authConfirmation, new DateTimeImmutable())) {
            $this->outputBoundary->present(
                DisplayVerifiedUpdatePageResult::createWhenValidationError('無効なワンタイムトークンです。')
            );
            return;
        }

        $this->outputBoundary->present(
            DisplayVerifiedUpdatePageResult::createWhenSuccess($oneTimeTokenValue, $authConfirmation->oneTimePassword())
        );
        return;
    }
}