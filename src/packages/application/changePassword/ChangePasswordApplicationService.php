<?php

namespace packages\application\changePassword;

use DateTimeImmutable;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;
use packages\domain\model\oauth\scope\ScopeList;
use packages\domain\service\authenticationAccount\AuthenticationService;
use packages\domain\service\authenticationAccount\LoggedInUserIdFetcher;
use RuntimeException;

class ChangePasswordApplicationService
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private LoggedInUserIdFetcher $loggedInUserIdFetcher;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        AuthenticationService $authenticationService,
        IScopeAuthorizationChecker $scopeAuthorizationChecker
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->loggedInUserIdFetcher = new LoggedInUserIdFetcher($authenticationService, $scopeAuthorizationChecker);
    }

    public function changePassword(
        string $scopeString,
        string $passwordString,
        string $redirectUrl
    ) 
    {
        $scope = Scope::from($scopeString);
        $userId = $this->loggedInUserIdFetcher->fetch($scope);

        $passwordValidation = new UserPasswordValidation($passwordString);
        if (!$passwordValidation->validate()) {

        }

        $authAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        if ($authAccount === null) {
            throw new RuntimeException('ユーザーが見つかりません');
        }

        $password = UserPassword::create($passwordString);
        $authAccount->changePassword($password, new DateTimeImmutable());
        $this->authenticationAccountRepository->save($authAccount);
    }
}