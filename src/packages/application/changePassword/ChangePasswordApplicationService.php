<?php

namespace packages\application\changePassword;

use DateTimeImmutable;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;
use packages\domain\model\oauth\scope\ScopeList;
use packages\domain\service\authenticationAccount\AuthenticationService;
use packages\domain\service\oauth\LoggedInUserIdFetcher;
use packages\domain\service\oauth\ClientService;
use RuntimeException;

class ChangePasswordApplicationService
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private LoggedInUserIdFetcher $loggedInUserIdFetcher;
    private ClientService $clientService;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        AuthenticationService $authenticationService,
        IScopeAuthorizationChecker $scopeAuthorizationChecker,
        IClientFetcher $clientFetcher
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->loggedInUserIdFetcher = new LoggedInUserIdFetcher($authenticationService, $scopeAuthorizationChecker);
        $this->clientService = new ClientService($clientFetcher);
    }

    public function changePassword(
        string $scopeString,
        string $passwordString,
        string $clientId,
        string $redirectUrl
    ): ChangePasswordResult
    {
        $scope = Scope::from($scopeString);
        $userId = $this->loggedInUserIdFetcher->fetch($scope);

        if (!$this->clientService->isCorrectRedirectUrl(
            new ClientId($clientId),
            new RedirectUrl($redirectUrl)
        )) {
            throw new RuntimeException('リダイレクトURLが正しくありません');
        }

        $passwordValidation = new UserPasswordValidation($passwordString);
        if (!$passwordValidation->validate()) {
            return ChangePasswordResult::createWhenFaild($passwordValidation->errorMessageList());
        }

        $authAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        if ($authAccount === null) {
            throw new RuntimeException('ユーザーが見つかりません');
        }

        $password = UserPassword::create($passwordString);
        $authAccount->changePassword($password, new DateTimeImmutable());
        $this->authenticationAccountRepository->save($authAccount);

        // 後でメール送信する処理を追加する

        return ChangePasswordResult::createWhenSuccess($redirectUrl);
    }
}