<?php

namespace packages\application\authentication\logout;

use Illuminate\Container\Attributes\Auth;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\service\authenticationAccount\AuthenticationService;

class LogoutApplicationService
{
    private IAccessTokenDeactivationService $accessTokenDeactivationService;
    private IRefreshTokenDeactivationService $refreshTokenDeactivationService;
    private AuthenticationService $authService;

    public function __construct(
        IAccessTokenDeactivationService $accessTokenDeactivationService,
        IRefreshTokenDeactivationService $refreshTokenDeactivationService,
        AuthenticationService $authService
    )
    {
        $this->accessTokenDeactivationService = $accessTokenDeactivationService;
        $this->refreshTokenDeactivationService = $refreshTokenDeactivationService;
        $this->authService = $authService;
    }

    public function logout(string $accessToken): void
    {
        $accessToken = new AccessToken($accessToken);

        $this->accessTokenDeactivationService->deactivate($accessToken);
        $this->refreshTokenDeactivationService->deactivate($accessToken);

        $this->authService->logout();
    }
}