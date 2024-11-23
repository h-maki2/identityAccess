<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\ResponseType;
use UnexpectedValueException;

class LoginApplicationService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;
    private LoginOutputBoundary $outputBoundary;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher,
        LoginOutputBoundary $outputBoundary
    )
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->sessionAuthentication = $sessionAuthentication;
        $this->clientFetcher = $clientFetcher;
        $this->outputBoundary = $outputBoundary;
    }

    public function login(
        string $inputedEmail,
        string $inputedPassword,
        string $clientId,
        string $redirectUrl,
        string $responseType
    ): void
    {
        $email = new UserEmail($inputedEmail);
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);

        if ($authenticationInformaion === null) {
            $this->outputBoundary->present(LoginResult::createWhenLoginFailed(false));
            return;
        }

        $currentDateTime = new DateTimeImmutable();
        if (!$authenticationInformaion->canLoggedIn($currentDateTime)) {
            $this->outputBoundary->present(LoginResult::createWhenLoginFailed(true));
            return;
        }

        if ($authenticationInformaion->canDisableLoginRestriction($currentDateTime)) {
            $authenticationInformaion->disableLoginRestriction($currentDateTime);
        }

        if ($authenticationInformaion->password()->equals($inputedPassword)) {
            $this->sessionAuthentication->markAsLoggedIn($authenticationInformaion->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode(
                $clientId,
                $redirectUrl,
                $responseType
            );

            $this->authenticationInformaionRepository->save($authenticationInformaion);
            $this->outputBoundary->present(LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode));
            return;
        }

        $authenticationInformaion->addFailedLoginCount($currentDateTime);
        if ($authenticationInformaion->canEnableLoginRestriction()) {
            $authenticationInformaion->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformaionRepository->save($authenticationInformaion);

        if (!$authenticationInformaion->canLoggedIn($currentDateTime)) {
            $this->outputBoundary->present(LoginResult::createWhenLoginFailed(true));
            return;
        }

        $this->outputBoundary->present(LoginResult::createWhenLoginFailed(false));
    }

    /**
     * 認可コード取得用URLを取得する
     */
    private function urlForObtainingAuthorizationCode(
        string $clientId,
        string $redirectUrl,
        string $responseType
    ): string
    {
        $clientId = new ClientId($clientId);
        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりません。");
        }

        $redirectUrl = new RedirectUrl($redirectUrl);
        return $client->urlForObtainingAuthorizationCode($redirectUrl, $responseType);
    }
}