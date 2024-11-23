<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationResult;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use RuntimeException;

/**
 * ワンタイムトークンとワンタイムパスワードの再生成を行うアプリケーションサービス
 */
class OneTimeTokenAndPasswordRegenerationApplicationService
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private OneTimeTokenAndPasswordRegenerationOutputBoundary $outputBoundary;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        OneTimeTokenAndPasswordRegenerationOutputBoundary $outputBoundary
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * ワンタイムトークンとワンタイムパスワードの再生成を行う
     */
    public function regenerateOneTimeTokenAndPassword(
        string $userEmailString
    ): void
    {
        $userEmail = new UserEmail($userEmailString);
        $authInfo = $this->authenticationInformaionRepository->findByEmail($userEmail);
        if ($authInfo === null) {
            $this->outputBoundary->present(
                OneTimeTokenAndPasswordRegenerationResult::createWhenValidationError('メールアドレスが登録されていません。')
            );
            return;
        }

        if ($authInfo->isVerified()) {
            $this->outputBoundary->present(
                OneTimeTokenAndPasswordRegenerationResult::createWhenValidationError('既にアカウントが認証済みです。')
            );
            return;
        }

        $userId = $authInfo->id();
        $authConfirmation = $this->authConfirmationRepository->findById($userId);
        if ($authConfirmation === null) {
            throw new RuntimeException('認証情報が存在しません。userId: ' . $userId->value);
        }
        
        $authConfirmation->reObtain();
        $this->authConfirmationRepository->save($authConfirmation);

        $this->outputBoundary->present(
            OneTimeTokenAndPasswordRegenerationResult::createWhenSuccess($authConfirmation->oneTimeToken()->value())
        );
    }
}