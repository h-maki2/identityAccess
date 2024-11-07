<?php

namespace packages\application\authentication;

use DateTimeImmutable;
use packages\domain\model\client\IClientFetcher;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SessionAuthentication;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\validation\LoginValidator;
use packages\domain\service\authentication\LoginFailureManager;
use UnexpectedValueException;

class LoginApplicationService
{
    private IUserProfileRepository $userProfileRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;
    private LoginFailureManager $loginFailureManager;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->sessionAuthentication = $sessionAuthentication;
        $this->clientFetcher = $clientFetcher;
        $this->loginFailureManager = new LoginFailureManager($userProfileRepository);
    }

    public function login(
        string $email,
        string $inputedPassword,
        string $clientId
    ): LoginResult
    {
        $email = new UserEmail($email);
        $userProfile = $this->userProfileRepository->findByEmail($email);

        $currentDateTime = new DateTimeImmutable();
        if (!LoginValidator::validate($userProfile, $inputedPassword, $currentDateTime)) {
            // ログインに失敗した場合
            $this->loginFailureManager->handleFailedLoginAttempt($userProfile, $currentDateTime);
            return LoginResult::createWhenLoginFailed($this->isAccountLocked($userProfile, $currentDateTime));
        }

        $this->sessionAuthentication->markAsLoggedIn($userProfile->id());

        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            throw new UnexpectedValueException("{$clientId}のクライアントが見つかりませんでした。");
        }

        return LoginResult::createWhenLoginSucceeded($client->authorizationUrl());
    }

    /**
     * アカウントがロックされているかどうかを判定する
     */
    private function isAccountLocked(?UserProfile $userProfile, DateTimeImmutable $currentDateTime): bool
    {
        if ($userProfile === null) {
            return false;
        }

        if ($userProfile->isLocked($currentDateTime)) {
            return true;
        }

        return false;
    }
}