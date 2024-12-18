<?php

namespace packages\domain\service\oneTimeTokenAndPasswordRegeneration;

use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\definitiveRegistrationCompletedEmailDtoFactory;
use RuntimeException;

class OneTimeTokenAndPasswordRegeneration
{
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private IEmailSender $emailSender;

    public function __construct(
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        IEmailSender $emailSender
    ) {
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->emailSender = $emailSender;
    }

    /**
     * ワンタイムトークンとワンタイムパスワードの再生成を行う
     * 再生成後に本登録確認メールメールを再送する
     */
    public function handle(AuthenticationAccount $authInfo)
    {
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findById($authInfo->id());
        if ($definitiveRegistrationConfirmation === null) {
            throw new RuntimeException('認証アカウントが存在しません。userId: ' . $authInfo->id()->value);
        }

        $definitiveRegistrationConfirmation->reObtain();
        $this->definitiveRegistrationConfirmationRepository->save($definitiveRegistrationConfirmation);

        $this->emailSender->send(
            DefinitiveRegistrationCompletedUpdateEmailDtoFactory::create(
                $authInfo->email(),
                $definitiveRegistrationConfirmation->oneTimeToken(),
                $definitiveRegistrationConfirmation->oneTimePassword()
            )
        );
    }
}