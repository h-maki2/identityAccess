<?php

namespace packages\domain\service\oneTimeTokenAndPasswordRegeneration;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\VerifiedUpdateEmailDtoFactory;
use RuntimeException;

class OneTimeTokenAndPasswordRegeneration
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IEmailSender $emailSender
    ) {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->emailSender = $emailSender;
    }

    /**
     * ワンタイムトークンとワンタイムパスワードの再生成を行う
     * 再生成後に本登録確認メールメールを再送する
     */
    public function handle(AuthenticationInformation $authInfo)
    {
        $authConfirmation = $this->authConfirmationRepository->findById($authInfo->id());
        if ($authConfirmation === null) {
            throw new RuntimeException('認証情報が存在しません。userId: ' . $authInfo->id()->value);
        }

        $authConfirmation->reObtain();
        $this->authConfirmationRepository->save($authConfirmation);

        $this->emailSender->send(
            VerifiedUpdateEmailDtoFactory::create(
                $authInfo->email(),
                $authConfirmation->oneTimeToken(),
                $authConfirmation->oneTimePassword()
            )
        );
    }
}