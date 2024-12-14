<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\scope\ScopeList;
use UnexpectedValueException;

class LoginApplicationService implements LoginInputBoundary
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationService $authService;
    private IClientFetcher $clientFetcher;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        AuthenticationService $authService,
        IClientFetcher $clientFetcher
    )
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authService = $authService;
        $this->clientFetcher = $clientFetcher;
    }

    public function login(
        string $inputedEmail,
        string $inputedPassword,
        string $clientId,
        string $redirectUrl,
        string $responseType,
        string $state,
        string $scopes
    ): LoginResult
    {
        $email = new UserEmail($inputedEmail);
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);

        if ($authenticationInformation === null) {
            return LoginResult::createWhenLoginFailed(false);
        }

        $currentDateTime = new DateTimeImmutable();
        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
            return LoginResult::createWhenLoginFailed(true);
        }

        if ($authenticationInformation->canDisableLoginRestriction($currentDateTime)) {
            $authenticationInformation->disableLoginRestriction($currentDateTime);
        }

        if ($authenticationInformation->password()->equals($inputedPassword)) {
            $this->authService->markAsLoggedIn($authenticationInformation->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode(
                $clientId,
                $redirectUrl,
                $responseType,
                $state,
                $scopes
            );

            $this->authenticationInformationRepository->save($authenticationInformation);
            return LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode);
        }

        $authenticationInformation->addFailedLoginCount($currentDateTime);
        if ($authenticationInformation->canEnableLoginRestriction()) {
            $authenticationInformation->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformationRepository->save($authenticationInformation);

        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
            return LoginResult::createWhenLoginFailed(true);
        }

        return LoginResult::createWhenLoginFailed(false);
    }

    /**
     * 認可コード取得用URLを取得する
     */
    private function urlForObtainingAuthorizationCode(
        string $clientId,
        string $redirectUrl,
        string $responseType,
        string $state,
        string $scopes
    ): string
    {
        $clientId = new ClientId($clientId);
        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりません。");
        }

        $redirectUrl = new RedirectUrl($redirectUrl);
        $scopeList = ScopeList::createFromString($scopes);
        return $client->urlForObtainingAuthorizationCode($redirectUrl, $responseType, $state, $scopeList);
    }
}