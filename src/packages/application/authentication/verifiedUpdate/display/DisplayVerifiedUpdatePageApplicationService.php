<?php

namespace packages\application\authentication\verifiedUpdate\display;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenValue;

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
    public function displayVerifiedUpdatePage(string $oneTimeToken)
    {
        $oneTimeToken = OneTimeTokenValue::reconstruct($oneTimeToken);
        $authConfirmation = $this->authConfirmationRepository->findByToken($oneTimeToken);
    }
}