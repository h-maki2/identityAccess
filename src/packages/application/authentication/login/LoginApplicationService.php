<?php

namespace packages\application\authentication\login;

use DateTimeImmutable;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\SessionAuthentication;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\ResponseType;
use UnexpectedValueException;

class LoginApplicationService implements LoginInputBoundary
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
            $this->sessionAuthentication->markAsLoggedIn($authenticationInformation->id());
            $urlForObtainingAuthorizationCode = $this->urlForObtainingAuthorizationCode(
                $clientId,
                $redirectUrl,
                $responseType
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