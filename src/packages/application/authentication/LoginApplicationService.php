<?php

namespace packages\application\authentication;

use DateTimeImmutable;
use packages\domain\model\client\IClientFetcher;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SessionAuthentication;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\validation\LoginValidator;

class LoginApplicationService
{
    private IUserProfileRepository $userProfileRepository;
    private SessionAuthentication $sessionAuthentication;
    private IClientFetcher $clientFetcher;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        SessionAuthentication $sessionAuthentication,
        IClientFetcher $clientFetcher
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->sessionAuthentication = $sessionAuthentication;
        $this->clientFetcher = $clientFetcher;
    }

    public function login(
        string $email,
        string $password,
        string $clientId
    ): LoginResult
    {
        $email = new UserEmail($email);
        $userProfile = $this->userProfileRepository->findByEmail($email);

        if ($userProfile === null) {
            return LoginResult::createWhenLoginFailed('ログインに失敗しました。');
        }

        $loginValidator = new LoginValidator();
        if (!$loginValidator->validate($userProfile, $password, new DateTimeImmutable())) {
            if (!$userProfile->isVerified()) {
                return LoginResult::createWhenLoginFailed('ログインに失敗しました。');
            }

            if ($userProfile->isLocked(new DateTimeImmutable())) {
                return LoginResult::createWhenLoginFailed('アカウントがロックされています。');
            }

            $userProfile->updateFailedLoginCount();
            if ($userProfile->hasReachedAccountLockoutThreshold()) {
                $userProfile->updateNextLoginAt();
            }
            $this->userProfileRepository->save($userProfile);
            return LoginResult::createWhenLoginFailed('ログインに失敗しました。');
        }

        $this->sessionAuthentication->markAsLoggedIn($userProfile->Id());

        $client = $this->clientFetcher->fetchById($clientId);
        if ($client === null) {
            // 例外を発生させる
        }

        return LoginResult::createWhenLoginSucceeded($client->authorizationUrl());
    }
}