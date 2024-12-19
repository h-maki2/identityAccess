<?php

namespace packages\application\registration\resendDefinitiveRegistrationConfirmation;

use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationResult;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\validation\UserEmailFormatChecker;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\registration\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegeneration;
use RuntimeException;

/**
 * 本登録確認メール再送のアプリケーションサービス
 */
class ResendDefinitiveRegistrationConfirmationApplicationService implements ResendDefinitiveRegistrationConfirmationInputBoundary
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
    public function handle(
        string $userEmailString
    ): ResendDefinitiveRegistrationConfirmationResult
    {
        $emailChecker = new UserEmailFormatChecker();
        if (!$emailChecker->validate($userEmailString)) {
            return ResendDefinitiveRegistrationConfirmationResult::createWhenValidationError('無効なメールアドレスです。');
        }

        $userEmail = new UserEmail($userEmailString);
        $authenticationAccount = $this->authenticationAccountRepository->findByEmail($userEmail);
        if ($authenticationAccount === null) {
            return ResendDefinitiveRegistrationConfirmationResult::createWhenValidationError('メールアドレスが登録されていません。');
        }

        if ($authenticationAccount->hasCompletedRegistration()) {
            return ResendDefinitiveRegistrationConfirmationResult::createWhenValidationError('既にアカウントが本登録済みです。');
        }

        $this->oneTimeTokenAndPasswordRegeneration->handle($authenticationAccount);

        return ResendDefinitiveRegistrationConfirmationResult::createWhenSuccess();
    }
}