<?php

namespace packages\domain\service\authentication;

use DateTimeImmutable;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;

class LoginFailureManager
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(IAuthenticationInformaionRepository $authenticationInformaionRepository)
    {
        $this->AuthenticationInformaionRepository = $authenticationInformaionRepository;
    }

    /**
     * ログイン失敗時の処理
     */
    public function handleFailedLoginAttempt(?AuthenticationInformaion $authenticationInformaion, DateTimeImmutable $currentDateTime): void
    {
        if ($authenticationInformaion === null) {
            return;
        }

        if (!$authenticationInformaion->isValid($currentDateTime)) {
            return;
        }

        $authenticationInformaion->updateFailedLoginCount();
        if ($authenticationInformaion->hasReachedAccountLockoutThreshold()) {
            $authenticationInformaion->updateNextLoginAt();
        }
        $this->AuthenticationInformaionRepository->save($authenticationInformaion);
    }
}