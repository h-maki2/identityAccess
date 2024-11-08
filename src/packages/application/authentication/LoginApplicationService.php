<?php

namespace packages\application\authentication;

use DateTimeImmutable;
use packages\domain\model\client\IClientFetcher;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\validation\LoginValidator;
use packages\domain\service\authentication\LoginFailureManager;
use UnexpectedValueException;

class LoginApplicationService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;
    private LoginFailureManager $loginFailureManager;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher
    )
    {
        $this->AuthenticationInformaionRepository = $authenticationInformaionRepository;
        $this->sessionAuthentication = $sessionAuthentication;
        $this->clientFetcher = $clientFetcher;
        $this->loginFailureManager = new LoginFailureManager($authenticationInformaionRepository);
    }

    public function login(
        string $email,
        string $inputedPassword,
        string $clientId
    ): LoginResult
    {
        $email = new UserEmail($email);
        $authenticationInformaion = $this->AuthenticationInformaionRepository->findByEmail($email);

        $currentDateTime = new DateTimeImmutable();
        if (!LoginValidator::validate($authenticationInformaion, $inputedPassword, $currentDateTime)) {
            // ログインに失敗した場合
            $this->loginFailureManager->handleFailedLoginAttempt($authenticationInformaion, $currentDateTime);
            return LoginResult::createWhenLoginFailed($this->isAccountLocked($authenticationInformaion, $currentDateTime));
        }

        $this->sessionAuthentication->markAsLoggedIn($authenticationInformaion->id());

        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりませんでした。");
        }

        return LoginResult::createWhenLoginSucceeded($client->authorizationUrl());
    }

    /**
     * アカウントがロックされているかどうかを判定する
     */
    private function isAccountLocked(?AuthenticationInformaion $authenticationInformaion, DateTimeImmutable $currentDateTime): bool
    {
        if ($authenticationInformaion === null) {
            return false;
        }

        if ($authenticationInformaion->isLocked($currentDateTime)) {
            return true;
        }

        return false;
    }
}