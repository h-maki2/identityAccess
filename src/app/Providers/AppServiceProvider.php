<?php

namespace App\Providers;

use App\Services\ApiVersionResolver;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
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
use packages\adapter\service\laravel\LaravelApiAuthenticationService;
use packages\adapter\service\laravel\LaravelAuthenticationService;
use packages\adapter\service\laravel\LaravelWebAuthenticationService;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\application\authentication\login\LoginApplicationService;
use packages\application\authentication\login\LoginInputBoundary;
use packages\application\changePassword\ChangePasswordApplicationInputBoundary;
use packages\application\changePassword\ChangePasswordApplicationService;
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
use packages\domain\service\oauth\LoggedInUserIdFetcherFromCookie;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
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

       // アプリケーションサービス
       $this->app->bind(LoginInputBoundary::class, LoginApplicationService::class);
       $this->app->bind(ResendDefinitiveRegistrationConfirmationInputBoundary::class, ResendDefinitiveRegistrationConfirmationApplicationService::class);
       $this->app->bind(DefinitiveRegistrationInputBoundary::class, DefinitiveRegistrationApplicationService::class);
       $this->app->bind(ProvisionalRegistrationInputBoundary::class, ProvisionalRegistrationApplicationService::class);
       $this->app->bind(ChangePasswordApplicationInputBoundary::class, ChangePasswordApplicationService::class);

       // クエリサービス
       $this->app->bind(AuthenticationService::class, LaravelAuthenticationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'read_account' => 'read account information',
            'edit_account' => 'edit account information',
            'delete_account' => 'delete account information',
        ]);
    }
}
