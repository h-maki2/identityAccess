<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\AuthenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\AuthenticationInformation\SessionAuthentication;
use packages\domain\model\AuthenticationInformation\UserEmail;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\ResponseType;
use UnexpectedValueException;

class LoginApplicationService
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;
    private LoginOutputBoundary $outputBoundary;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher,
        LoginOutputBoundary $outputBoundary
    )
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
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
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);

        if ($authenticationInformation === null) {
            $this->outputBoundary->present(LoginResult::createWhenLoginFailed(false));
            return;
        }

        $currentDateTime = new DateTimeImmutable();
        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
            $this->outputBoundary->present(LoginResult::createWhenLoginFailed(true));
            return;
        }

        if ($authenticationInformation->canDisableLoginRestriction($currentDateTime)) {
            $authenticationInformation->disableLoginRestriction($currentDateTime);
        }

        if ($authenticationInformation->password()->equals($inputedPassword)) {
            $this->sessionAuthentication->markAsLoggedIn($authenticationInformation->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode(
                $clientId,
                $redirectUrl,
                $responseType
            );

            $this->authenticationInformationRepository->save($authenticationInformation);
            $this->outputBoundary->present(LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode));
            return;
        }

        $authenticationInformation->addFailedLoginCount($currentDateTime);
        if ($authenticationInformation->canEnableLoginRestriction()) {
            $authenticationInformation->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformationRepository->save($authenticationInformation);

        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
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