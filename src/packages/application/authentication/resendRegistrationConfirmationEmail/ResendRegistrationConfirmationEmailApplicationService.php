<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegeneration;
use RuntimeException;

/**
 * 本登録確認メール再送のアプリケーションサービス
 */
class ResendRegistrationConfirmationEmailApplicationService implements ResendRegistrationConfirmationEmailInputBoundary
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private ResendRegistrationConfirmationEmailOutputBoundary $outputBoundary;
    private OneTimeTokenAndPasswordRegeneration $oneTimeTokenAndPasswordRegeneration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository,
        ResendRegistrationConfirmationEmailOutputBoundary $outputBoundary,
        IEmailSender $emailSender
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->outputBoundary = $outputBoundary;
        $this->oneTimeTokenAndPasswordRegeneration = new OneTimeTokenAndPasswordRegeneration(
            $this->authConfirmationRepository,
            $emailSender
        );
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

        $this->oneTimeTokenAndPasswordRegeneration->handle($authInfo);

        $this->outputBoundary->formatForResponse(
            ResendRegistrationConfirmationEmailResult::createWhenSuccess()
        );
        return $this->outputBoundary;
    }
}