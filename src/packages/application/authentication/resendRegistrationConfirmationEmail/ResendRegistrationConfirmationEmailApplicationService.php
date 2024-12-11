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
    private OneTimeTokenAndPasswordRegeneration $oneTimeTokenAndPasswordRegeneration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IEmailSender $emailSender
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
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
    ): ResendRegistrationConfirmationEmailResult
    {
        $userEmail = new UserEmail($userEmailString);
        $authInfo = $this->authenticationInformationRepository->findByEmail($userEmail);
        if ($authInfo === null) {
            return ResendRegistrationConfirmationEmailResult::createWhenValidationError('メールアドレスが登録されていません。');
        }

        if ($authInfo->isVerified()) {
            return ResendRegistrationConfirmationEmailResult::createWhenValidationError('既にアカウントが認証済みです。');
        }

        $this->oneTimeTokenAndPasswordRegeneration->handle($authInfo);

        return ResendRegistrationConfirmationEmailResult::createWhenSuccess();
    }
}