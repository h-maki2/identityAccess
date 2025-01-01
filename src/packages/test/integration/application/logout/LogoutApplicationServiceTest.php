<?php

use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use packages\adapter\oauth\authToken\LaravelPassportAccessTokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshTokenDeactivationService;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\service\laravel\LaravelAuthenticationService;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\application\authentication\logout\LogoutApplicationService;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\oauth\authToken\AuthTokenTestDataCreator;
use packages\test\helpers\oauth\authToken\TestAuthTokenService;
use packages\test\helpers\oauth\client\AccessTokenTestDataCreator;
use Tests\TestCase;

class LogoutApplicationServiceTest extends TestCase
{
    use DatabaseTransactions;

    private EloquentAuthenticationAccountRepository $authAccountReposiotry;
    private LaravelPassportAccessTokenDeactivationService $accessTokenDeactivationService;
    private LaravelPassportRefreshTokenDeactivationService $refreshTokenDeactivationService;
    private LogoutApplicationService $logoutApplicationService;
    private LaravelAuthenticationService $authService;
    private TestAuthTokenService $testAuthTokenService;
    private AuthTokenTestDataCreator $authTokenTestDataCreator;

    public function setUp(): void
    {
        parent::setUp();

        $this->authAccountReposiotry = new EloquentAuthenticationAccountRepository();
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

        $this->authTokenTestDataCreator = new AuthTokenTestDataCreator(
            new AuthenticationAccountTestDataCreator($this->authAccountReposiotry)
        );
    }

    public function test_ログアウトするとアクセストークンとリフレッシュトークンが無効化される()
    {
        // given
        // アクセストークンとリフレッシュトークンを作成
        $authToken = $this->authTokenTestDataCreator->create();

        // when
        $this->logoutApplicationService->logout(
            $authToken->accessToken->value, 
            $authToken->refreshToken->value
        );

        // then
        // アクセストークンが無効化されていることを確認
        $this->assertTrue($this->testAuthTokenService->isAccessTokenDeactivated($authToken->accessToken));

        // リフレッシュトークンが無効化されていることを確認
        $this->assertTrue($this->testAuthTokenService->isRefreshTokenDeactivated($authToken->refreshToken));
    }
}