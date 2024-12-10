<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use UnexpectedValueException;

class LoginApplicationService implements LoginInputBoundary
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationService $authService;
    private IClientFetcher $clientFetcher;
    private LoginOutputBoundary $outputBoundary;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        AuthenticationService $authService,
        IClientFetcher $clientFetcher,
        LoginOutputBoundary $outputBoundary
    )
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authService = $authService;
        $this->clientFetcher = $clientFetcher;
        $this->outputBoundary = $outputBoundary;
    }

    public function login(
        string $inputedEmail,
        string $inputedPassword,
        string $clientId,
        string $redirectUrl,
        string $responseType,
        string $state
    ): LoginOutputBoundary
    {
        $email = new UserEmail($inputedEmail);
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);

        if ($authenticationInformation === null) {
            $this->outputBoundary->formatForResponse(LoginResult::createWhenLoginFailed(false));
            return $this->outputBoundary;
        }

        $currentDateTime = new DateTimeImmutable();
        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
            $this->outputBoundary->formatForResponse(LoginResult::createWhenLoginFailed(true));
            return $this->outputBoundary;
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
                $state
            );

            $this->authenticationInformationRepository->save($authenticationInformation);
            $this->outputBoundary->formatForResponse(LoginResult::createWhenLoginSucceeded($urlForObtainingAuthorizationCode));
            return $this->outputBoundary;
        }

        $authenticationInformation->addFailedLoginCount($currentDateTime);
        if ($authenticationInformation->canEnableLoginRestriction()) {
            $authenticationInformation->enableLoginRestriction($currentDateTime);
        }
        $this->authenticationInformationRepository->save($authenticationInformation);

        if (!$authenticationInformation->canLoggedIn($currentDateTime)) {
            $this->outputBoundary->formatForResponse(LoginResult::createWhenLoginFailed(true));
            return $this->outputBoundary;
        }

        $this->outputBoundary->formatForResponse(LoginResult::createWhenLoginFailed(false));
        return $this->outputBoundary;
    }

    /**
     * 認可コード取得用URLを取得する
     */
    private function urlForObtainingAuthorizationCode(
        string $clientId,
        string $redirectUrl,
        string $responseType,
        string $state
    ): string
    {
        $clientId = new ClientId($clientId);
        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりません。");
        }

        $redirectUrl = new RedirectUrl($redirectUrl);
        return $client->urlForObtainingAuthorizationCode($redirectUrl, $responseType, $state);
    }
}