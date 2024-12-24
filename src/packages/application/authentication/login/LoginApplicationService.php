<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\validation\UserEmailFormatChecker;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\scope\ScopeList;
use packages\domain\service\authenticationAccount\AuthenticationService;
use UnexpectedValueException;

class LoginApplicationService implements LoginInputBoundary
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationService $authService;
    private IClientFetcher $clientFetcher;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        AuthenticationService $authService,
        IClientFetcher $clientFetcher
    )
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
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
        $emailFormatChecker = new UserEmailFormatChecker();
        if (!$emailFormatChecker->validate($inputedEmail)) {
            return LoginResult::createWhenLoginFailed(false);
        }

        $email = new UserEmail($inputedEmail);
        $authenticationAccount = $this->authenticationAccountRepository->findByEmail($email);

        if ($authenticationAccount === null) {
            return LoginResult::createWhenLoginFailed(false);
        }

        $currentDateTime = new DateTimeImmutable();
        if (!$authenticationAccount->canLoggedIn($currentDateTime)) {
            return LoginResult::createWhenLoginFailed(true);
        }

        if ($authenticationAccount->canUnlocking($currentDateTime)) {
            $authenticationAccount->unlocking($currentDateTime);
        }

        if ($authenticationAccount->password()->equals($inputedPassword)) {
            $this->authService->markAsLoggedIn($authenticationAccount->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode(
                $clientId,
                $redirectUrl,
                $responseType,
                $state,
                $scopes
            );

            $this->authenticationAccountRepository->save($authenticationAccount);
            return LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode);
        }

        $authenticationAccount->addFailedLoginCount($currentDateTime);
        if ($authenticationAccount->canLocking()) {
            $authenticationAccount->locking($currentDateTime);
        }
        $this->authenticationAccountRepository->save($authenticationAccount);

        if (!$authenticationAccount->canLoggedIn($currentDateTime)) {
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