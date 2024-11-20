<?php

namespace packages\application\authentication\authConfirmationRegeneration;

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

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformaionRepository $authenticationInformaionRepository
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
    }

    /**
     * ワンタイムトークンとワンタイムパスワードの再生成を行う
     */
    public function regenerateOneTimeTokenAndPassword(
        string $userEmailString
    ): OneTimeTokenAndPasswordRegenerationResult
    {
        $userEmail = new UserEmail($userEmailString);
        $authInfo = $this->authenticationInformaionRepository->findByEmail($userEmail);
        if ($authInfo === null) {
            return OneTimeTokenAndPasswordRegenerationResult::createWhenValidationError('メールアドレスが登録されていません。');
        }

        if ($authInfo->isVerified()) {
            return OneTimeTokenAndPasswordRegenerationResult::createWhenValidationError('既にアカウントが認証済みです。');
        }

        $userId = $authInfo->id();
        $authConfirmation = $this->authConfirmationRepository->findById($userId);
        if ($authConfirmation === null) {
            throw new RuntimeException('認証情報が存在しません。userId: ' . $userId->value);
        }
        
        $authConfirmation->reObtain();
        $this->authConfirmationRepository->save($authConfirmation);

        return OneTimeTokenAndPasswordRegenerationResult::createWhenSuccess($authConfirmation->oneTimeToken()->value());
    }
}