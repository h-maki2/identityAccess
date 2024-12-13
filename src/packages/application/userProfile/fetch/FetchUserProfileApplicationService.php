<?php

namespace packages\application\userProfile\fetch;

use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\userProfile\IUserProfileRepository;

class FetchUserProfileApplicationService
{
    private IUserProfileRepository $userProfileRepository;
    private AuthenticationService $authService;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        AuthenticationService $authService
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->authService = $authService;
    }

    public function handle()
    {
        
    }
}