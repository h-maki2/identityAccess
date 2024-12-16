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
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\persistence\eloquent\EloquentUserProfileRepository;
use packages\adapter\service\laravel\LaravelAuthenticationService;
use packages\adapter\service\laravel\LaravelSessionAuthentication;
use packages\adapter\unitOfWork\EloquentUnitOfWork;
use packages\application\authentication\login\LoginApplicationService;
use packages\application\authentication\login\LoginInputBoundary;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailApplicationService;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailInputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageApplicationService;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageInputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateApplicationService;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateInputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\userProfile\fetch\FetchUserProfileApplicationService;
use packages\application\userProfile\fetch\FetchUserProfileInputBoundary;
use packages\application\userProfile\register\RegisterUserProfileApplicationService;
use packages\application\userProfile\register\RegisterUserProfileInputBoundary;
use packages\application\userProfile\register\RegisterUserProfileOutputBoundary;
use packages\application\userRegistration\UserRegistrationApplicationService;
use packages\application\userRegistration\UserRegistrationInputBoundary;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\SessionAuthentication;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\userProfile\IUserProfileRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // リポジトリ
        $this->app->bind(IAuthConfirmationRepository::class, EloquentAuthConfirmationRepository::class);
        $this->app->bind(IAuthenticationInformationRepository::class, EloquentAuthenticationInformationRepository::class);
        $this->app->bind(IUserProfileRepository::class, EloquentUserProfileRepository::class);

        // Laravel Passport
        $this->app->bind(IClientFetcher::class, LaravelPassportClientFetcher::class);
        $this->app->bind(IAccessTokenDeactivationService::class, LaravelPassportAccessTokenDeactivationService::class);
        $this->app->bind(IRefreshTokenDeactivationService::class, LaravelPassportRefreshokenDeactivationService::class);
        $this->app->bind(IScopeAuthorizationChecker::class, LaravelPassportScopeAuthorizationChecker::class);

        // ユニットオブワーク
        $this->app->bind(UnitOfWork::class, EloquentUnitOfWork::class);

        // サービス
        $this->app->bind(AuthenticationService::class, LaravelAuthenticationService::class);

        // アプリケーションサービス
        $this->app->bind(LoginInputBoundary::class, LoginApplicationService::class);
        $this->app->bind(ResendRegistrationConfirmationEmailInputBoundary::class, ResendRegistrationConfirmationEmailApplicationService::class);
        $this->app->bind(VerifiedUpdateInputBoundary::class, VerifiedUpdateApplicationService::class);
        $this->app->bind(UserRegistrationInputBoundary::class, UserRegistrationApplicationService::class);
        $this->app->bind(RegisterUserProfileInputBoundary::class, RegisterUserProfileApplicationService::class);
        $this->app->bind(FetchUserProfileInputBoundary::class, FetchUserProfileApplicationService::class);

        // その他　フレームワークに関する設定
        $this->app->bind(ApiVersionResolver::class, ApiVersionResolver::class);

        // メール送信
        $this->app->bind(IEmailSender::class, LaravelEmailSender::class);
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
