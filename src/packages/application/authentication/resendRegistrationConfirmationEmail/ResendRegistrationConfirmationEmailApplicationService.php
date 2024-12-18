<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
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
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private OneTimeTokenAndPasswordRegeneration $oneTimeTokenAndPasswordRegeneration;

    public function __construct(
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IEmailSender $emailSender
    )
    {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->oneTimeTokenAndPasswordRegeneration = new OneTimeTokenAndPasswordRegeneration(
            $this->definitiveRegistrationConfirmationRepository,
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