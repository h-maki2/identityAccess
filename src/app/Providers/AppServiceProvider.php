<?php

namespace App\Providers;

use App\Services\ApiVersionResolver;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use packages\adapter\email\LaravelEmailSender;
use packages\adapter\oauth\authToken\LaravelPassportAccessTokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshokenDeactivationService;
use packages\adapter\oauth\client\LaravelPassportClientFetcher;
use packages\adapter\oauth\scope\LaravelPassportScopeAuthorizationChecker;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\persistence\eloquent\EloquentUserProfileRepository;
use packages\adapter\service\laravel\LaravelAuthenticationService;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\application\authentication\login\LoginApplicationService;
use packages\application\authentication\login\LoginInputBoundary;
use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationApplicationService;
use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationInputBoundary;
use packages\application\registration\definitiveRegistration\DefinitiveRegistrationApplicationService;
use packages\application\registration\definitiveRegistration\DefinitiveRegistrationInputBoundary;
use packages\application\userProfile\fetch\FetchUserProfileApplicationService;
use packages\application\userProfile\fetch\FetchUserProfileInputBoundary;
use packages\application\userProfile\register\RegisterUserProfileApplicationService;
use packages\application\userProfile\register\RegisterUserProfileInputBoundary;
use packages\application\registration\provisionalRegistration\ProvisionalRegistrationApplicationService;
use packages\application\registration\provisionalRegistration\ProvisionalRegistrationInputBoundary;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\service\authenticationAccount\AuthenticationService;
use packages\domain\service\oauth\LoggedInUserIdFetcher;
use packages\domain\service\oauth\LoggedInUserIdFetcher;
use packages\domain\service\oauth\LoggedInUserIdFetcherFromCookie;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       $this->registerCommonBindings();
       
        // リクエストがAPIかWebかでバインディングを切り替える
        $request = $this->app->make('request');
        if ($request->is('api/*')) {
            $this->registerApiBindings();
        } else {
            $this->registerWebBindings();
        }
    }

    protected function registerCommonBindings(): void
    {
        // リポジトリ
        $this->app->bind(IDefinitiveRegistrationConfirmationRepository::class, EloquentDefinitiveRegistrationConfirmationRepository::class);
        $this->app->bind(IAuthenticationAccountRepository::class, EloquentAuthenticationAccountRepository::class);
        $this->app->bind(IUserProfileRepository::class, EloquentUserProfileRepository::class);

        // Laravel Passport
        $this->app->bind(IClientFetcher::class, LaravelPassportClientFetcher::class);
        $this->app->bind(IAccessTokenDeactivationService::class, LaravelPassportAccessTokenDeactivationService::class);
        $this->app->bind(IRefreshTokenDeactivationService::class, LaravelPassportRefreshokenDeactivationService::class);
        $this->app->bind(IScopeAuthorizationChecker::class, LaravelPassportScopeAuthorizationChecker::class);

        // ユニットオブワーク
        $this->app->bind(TransactionManage::class, EloquentTransactionManage::class);

        // メール送信
        $this->app->bind(IEmailSender::class, LaravelEmailSender::class);
    }

    protected function registerWebBindings(): void
    {
        // アプリケーションサービス
        $this->app->bind(LoginInputBoundary::class, LoginApplicationService::class);
        $this->app->bind(ResendDefinitiveRegistrationConfirmationInputBoundary::class, ResendDefinitiveRegistrationConfirmationApplicationService::class);
        $this->app->bind(DefinitiveRegistrationInputBoundary::class, DefinitiveRegistrationApplicationService::class);
        $this->app->bind(ProvisionalRegistrationInputBoundary::class, ProvisionalRegistrationApplicationService::class);
        $this->app->bind(RegisterUserProfileInputBoundary::class, RegisterUserProfileApplicationService::class);
        $this->app->bind(FetchUserProfileInputBoundary::class, FetchUserProfileApplicationService::class);

        // サービス
        $this->app->bind(AuthenticationService::class, LaravelAuthenticationService::class);

        // 認証系
        $this->app->bind(LoggedInUserIdFetcher::class, LoggedInUserIdFetcherFromCookie::class);
    }

    protected function registerApiBindings(): void
    {
        // その他　フレームワークに関する設定
        $this->app->bind(ApiVersionResolver::class, ApiVersionResolver::class);

        // 認証系
        $this->app->bind(LoggedInUserIdFetcher::class, LoggedInUserIdFetcher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'view-profile' => 'View user profile information',
            'edit-profile' => 'Edit user profile information',
            'register-profile' => 'Register user profile information',
        ]);
    }
}
