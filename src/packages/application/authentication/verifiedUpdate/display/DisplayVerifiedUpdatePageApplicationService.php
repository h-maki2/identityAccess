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

    public function __construct(IAuthConfirmationRepository $authConfirmationRepository)
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
    }

    /**
     * 認証済み更新ページを表示する
     */
    public function displayVerifiedUpdatePage(string $oneTimeTokenValueString): DisplayVerifiedUpdatePageResult
    {
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct($oneTimeTokenValueString);
        $authConfirmation = $this->authConfirmationRepository->findByToken($oneTimeTokenValue);
        if (!AuthConfirmationValidation::validateExpirationDate($authConfirmation, new DateTimeImmutable())) {
            return DisplayVerifiedUpdatePageResult::createWhenValidationError('無効なワンタイムトークンです。');
        }

        return DisplayVerifiedUpdatePageResult::createWhenSuccess($oneTimeTokenValue, $authConfirmation->oneTimePassword());
    }
}