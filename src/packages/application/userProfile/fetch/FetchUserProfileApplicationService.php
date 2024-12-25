<?php

namespace packages\application\userProfile\fetch;

use packages\domain\model\authenticationAccount\AuthenticationService;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\service\oauth\LoggedInUserIdFetcher;

class FetchUserProfileApplicationService implements FetchUserProfileInputBoundary
{
    private IUserProfileRepository $userProfileRepository;
    private LoggedInUserIdFetcher $loggedInUserIdFetcher;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        AuthenticationService $authService,
        IScopeAuthorizationChecker $scopeAuthorizationChecker
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->loggedInUserIdFetcher = new LoggedInUserIdFetcher($authService, $scopeAuthorizationChecker);
    }

    public function handle(string $scope): FetchUserProfileResult
    {
        $userId = $this->loggedInUserIdFetcher->fetch(Scope::from($scope));

        $userProfile = $this->userProfileRepository->findByUserId($userId);

        if ($userProfile === null) {
            return FetchUserProfileResult::createWhenNotFound();
        }

        return FetchUserProfileResult::createWhenFound(
            $userProfile->profileId()->value,
            $userProfile->name()->value,
            $userProfile->selfIntroductionText()->value
        );
    }
}