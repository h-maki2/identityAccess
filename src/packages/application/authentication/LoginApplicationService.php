<?php

namespace packages\application\authentication;

use DateTimeImmutable;
use packages\domain\model\client\IClientFetcher;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
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
            return LoginResult::createWhenLoginFailed(false);
        }

        $currentDateTime = new DateTimeImmutable();
        if ($authenticationInformaion->isLocked($currentDateTime)) {
            return LoginResult::createWhenLoginFailed(true);
        }

        if ($authenticationInformaion->isUnderLoginRestriction()) {
            $authenticationInformaion->disableLoginRestriction($currentDateTime);
        }

        if ($authenticationInformaion->password()->equals($inputedPassword)) {
            $this->sessionAuthentication->markAsLoggedIn($authenticationInformaion->id());

            $client = $this->clientFetcher->fetchById($clientId);
            if ($client === null) {
                throw new UnexpectedValueException("{$clientId}のクライアントが見つかりません。");
            }

            $this->authenticationInformaionRepository->save($authenticationInformaion);
            return LoginResult::createWhenLoginSucceeded($client->urlForObtainingAuthorizationCode());
        }

        $authenticationInformaion->addFailedLoginCount($currentDateTime);
        if ($authenticationInformaion->canEnableLoginRestriction()) {
            $authenticationInformaion->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformaionRepository->save($authenticationInformaion);

        if ($authenticationInformaion->isLocked($currentDateTime)) {
            return LoginResult::createWhenLoginFailed(true);
        }

        return LoginResult::createWhenLoginFailed(false);
    }
}