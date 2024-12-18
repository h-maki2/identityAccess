<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegeneration;
use RuntimeException;

/**
 * 本登録確認メール再送のアプリケーションサービス
 */
class ResendRegistrationConfirmationEmailApplicationService implements ResendRegistrationConfirmationEmailInputBoundary
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private OneTimeTokenAndPasswordRegeneration $oneTimeTokenAndPasswordRegeneration;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IEmailSender $emailSender
    )
    {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
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
        $authInfo = $this->authenticationAccountRepository->findByEmail($userEmail);
        if ($authInfo === null) {
            return ResendRegistrationConfirmationEmailResult::createWhenValidationError('メールアドレスが登録されていません。');
        }

        if ($authInfo->isVerified()) {
            return ResendRegistrationConfirmationEmailResult::createWhenValidationError('既にアカウントが確認済みです。');
        }

        $this->oneTimeTokenAndPasswordRegeneration->handle($authInfo);

        return ResendRegistrationConfirmationEmailResult::createWhenSuccess();
    }
}