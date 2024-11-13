<?php

namespace packages\application\authentication;

use DateTimeImmutable;
use packages\domain\model\client\IClientFetcher;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\validation\LoginAvailabilityService;
use UnexpectedValueException;

class LoginApplicationService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->sessionAuthentication = $sessionAuthentication;
        $this->clientFetcher = $clientFetcher;
    }

    public function login(
        string $email,
        string $inputedPassword,
        string $clientId
    ): LoginResult
    {
        $email = new UserEmail($email);
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);

        if ($authenticationInformaion === null) {
            return LoginResult::createWhenLoginFailed();
        }

        $currentDateTime = new DateTimeImmutable();
        if (LoginAvailabilityService::isLoginAvailable($authenticationInformaion, $inputedPassword, $currentDateTime)) {
            $this->sessionAuthentication->markAsLoggedIn($authenticationInformaion->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode($clientId);
            return LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode);
        }

        $authenticationInformaion->addFailedLoginCount($currentDateTime);
        if ($authenticationInformaion->canEnableLoginRestriction()) {
            $authenticationInformaion->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformaionRepository->save($authenticationInformaion);

        return LoginResult::createWhenLoginFailed();
    }

    /**
     * 認可コード取得用URLを取得する
     */
    private function urlForObtainingAuthorizationCode(string $clientId): string
    {
        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりません。");
        }
            
        return $client->urlForObtainingAuthorizationCode();
    }
}