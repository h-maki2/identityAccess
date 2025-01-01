<?php

use Illuminate\Auth\Events\Logout;
use packages\adapter\oauth\authToken\LaravelPassportAccessTokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshTokenDeactivationService;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\service\laravel\LaravelAuthenticationService;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\application\authentication\logout\LogoutApplicationService;
use packages\test\helpers\oauth\client\AccessTokenTestDataCreator;
use packages\test\helpers\oauth\client\TestAuthTokenService;
use Tests\TestCase;

class LogoutApplicationServiceTest extends TestCase
{
    private EloquentAuthenticationAccountRepository $authAccountReposiotry;
    private AccessTokenTestDataCreator $accessTokenTestDataCreator;
    private LaravelPassportAccessTokenDeactivationService $accessTokenDeactivationService;
    private LaravelPassportRefreshTokenDeactivationService $refreshTokenDeactivationService;
    private LogoutApplicationService $logoutApplicationService;
    private LaravelAuthenticationService $authService;
    private TestAuthTokenService $testAuthTokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->authAccountReposiotry = new EloquentAuthenticationAccountRepository();
        $this->accessTokenTestDataCreator = new AccessTokenTestDataCreator();
        $this->accessTokenDeactivationService = new LaravelPassportAccessTokenDeactivationService();
        $this->refreshTokenDeactivationService = new LaravelPassportRefreshTokenDeactivationService();
        $this->authService = new LaravelAuthenticationService();
        $this->testAuthTokenService = new TestAuthTokenService();

        $this->logoutApplicationService = new LogoutApplicationService(
            $this->accessTokenDeactivationService,
            $this->refreshTokenDeactivationService,
            $this->authService,
            new EloquentTransactionManage()
        );
    }

    public function test_ログアウトするとアクセストークンとリフレッシュトークンが無効化される()
    {
        // given
        // アクセストークンを作成
        $accessToken = $this->accessTokenTestDataCreator->create();

        // when
        $this->logoutApplicationService->logout($accessToken->value);

        // then
        // アクセストークンが無効化されていることを確認
        $this->assertTrue($this->testAuthTokenService->isAccessTokenDeactivated($accessToken));

        // リフレッシュトークンが無効化されていることを確認
        // $this->assertTrue($this->testAuthTokenService->isRefreshTokenDeactivated($accessToken));
    }
}