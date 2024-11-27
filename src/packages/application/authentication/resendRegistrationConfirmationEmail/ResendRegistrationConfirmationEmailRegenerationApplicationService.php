<?php

namespace packages\application\authentication\ResendRegistrationConfirmationEmail;

use packages\application\authentication\ResendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use RuntimeException;

/**
 * 本登録確認メール再送のアプリケーションサービス
 */
class ResendRegistrationConfirmationEmailApplicationService implements ResendRegistrationConfirmationEmailInputBoundary
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private ResendRegistrationConfirmationEmailOutputBoundary $outputBoundary;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository,
        ResendRegistrationConfirmationEmailOutputBoundary $outputBoundary
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * 本人確認メールの再送を行う
     */
    public function resendRegistrationConfirmationEmail(
        string $userEmailString
    ): ResendRegistrationConfirmationEmailOutputBoundary
    {
        $userEmail = new UserEmail($userEmailString);
        $authInfo = $this->authenticationInformationRepository->findByEmail($userEmail);
        if ($authInfo === null) {
            $this->outputBoundary->formatForResponse(
                ResendRegistrationConfirmationEmailResult::createWhenValidationError('メールアドレスが登録されていません。')
            );
            return $this->outputBoundary;
        }

        if ($authInfo->isVerified()) {
            $this->outputBoundary->formatForResponse(
                ResendRegistrationConfirmationEmailResult::createWhenValidationError('既にアカウントが認証済みです。')
            );
            return $this->outputBoundary;
        }

        $userId = $authInfo->id();
        $authConfirmation = $this->authConfirmationRepository->findById($userId);
        if ($authConfirmation === null) {
            throw new RuntimeException('認証情報が存在しません。userId: ' . $userId->value);
        }
        
        $authConfirmation->reObtain();
        $this->authConfirmationRepository->save($authConfirmation);

        $this->outputBoundary->formatForResponse(
            ResendRegistrationConfirmationEmailResult::createWhenSuccess($authConfirmation->oneTimeToken()->value())
        );
        return $this->outputBoundary;
    }
}