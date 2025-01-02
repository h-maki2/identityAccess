<?php

namespace packages\application\authentication\logout;

use Illuminate\Container\Attributes\Auth;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\authToken\RefreshToken;
use packages\domain\service\authenticationAccount\AuthenticationService;

class LogoutApplicationService
{
    private IAccessTokenDeactivationService $accessTokenDeactivationService;
    private IRefreshTokenDeactivationService $refreshTokenDeactivationService;
    private AuthenticationService $authService;
    private TransactionManage $transactionManage;

    public function __construct(
        IAccessTokenDeactivationService $accessTokenDeactivationService,
        IRefreshTokenDeactivationService $refreshTokenDeactivationService,
        AuthenticationService $authService,
        TransactionManage $transactionManage
    )
    {
        $this->accessTokenDeactivationService = $accessTokenDeactivationService;
        $this->refreshTokenDeactivationService = $refreshTokenDeactivationService;
        $this->authService = $authService;
        $this->transactionManage = $transactionManage;
    }

    public function logout(string $accessToken): void
    {
        $accessToken = new AccessToken($accessToken);
        try {
            $this->transactionManage->performTransaction(function () use ($accessToken) {
                $this->accessTokenDeactivationService->deactivate($accessToken);
                $this->refreshTokenDeactivationService->deactivate($accessToken);
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        $this->authService->logout();
    }
}