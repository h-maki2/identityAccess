<?php

namespace App\Providers;

use App\Services\ApiVersionResolver;
use Illuminate\Support\ServiceProvider;
use packages\adapter\email\LaravelEmailSender;
use packages\adapter\oauth\authToken\LaravelPassportAccessTokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshokenDeactivationService;
use packages\adapter\oauth\client\LaravelPassportClientFetcher;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\presenter\authentication\login\json\JsonLoginPresenter;
use packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json\JsonResendRegistrationConfirmationEmailPresenter;
use packages\adapter\presenter\authentication\verifiedUpdate\json\JsonDisplayVerifiedUpdatePagePresenter;
use packages\adapter\presenter\authentication\verifiedUpdate\json\JsonVerifiedUpdatePresenter;
use packages\adapter\presenter\errorResponse\ErrorResponse;
use packages\adapter\presenter\errorResponse\JsonErrorResponse;
use packages\adapter\presenter\userRegistration\json\JsonUserRegistrationPresenter;
use packages\adapter\session\LaravelSessionAuthentication;
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
use packages\application\userRegistration\UserRegistrationApplicationService;
use packages\application\userRegistration\UserRegistrationInputBoundary;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\SessionAuthentication;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\client\IClientFetcher;

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

        // Laravel Passport
        $this->app->bind(IClientFetcher::class, LaravelPassportClientFetcher::class);
        $this->app->bind(IAccessTokenDeactivationService::class, LaravelPassportAccessTokenDeactivationService::class);
        $this->app->bind(IRefreshTokenDeactivationService::class, LaravelPassportRefreshokenDeactivationService::class);

        // ユニットオブワーク
        $this->app->bind(UnitOfWork::class, EloquentUnitOfWork::class);

        // プレゼンター
        $this->app->bind(LoginOutputBoundary::class, JsonLoginPresenter::class);
        $this->app->bind(DisplayVerifiedUpdatePageOutputBoundary::class, JsonDisplayVerifiedUpdatePagePresenter::class);
        $this->app->bind(VerifiedUpdateOutputBoundary::class, JsonVerifiedUpdatePresenter::class);
        $this->app->bind(ResendRegistrationConfirmationEmailOutputBoundary::class, JsonResendRegistrationConfirmationEmailPresenter::class);
        $this->app->bind(UserRegistrationOutputBoundary::class, JsonUserRegistrationPresenter::class);
        $this->app->bind(ErrorResponse::class, JsonErrorResponse::class);

        // Laravel
        $this->app->bind(SessionAuthentication::class, LaravelSessionAuthentication::class);

        // アプリケーションサービス
        $this->app->bind(LoginInputBoundary::class, LoginApplicationService::class);
        $this->app->bind(ResendRegistrationConfirmationEmailInputBoundary::class, ResendRegistrationConfirmationEmailApplicationService::class);
        $this->app->bind(DisplayVerifiedUpdatePageInputBoundary::class, DisplayVerifiedUpdatePageApplicationService::class);
        $this->app->bind(VerifiedUpdateInputBoundary::class, VerifiedUpdateApplicationService::class);
        $this->app->bind(UserRegistrationInputBoundary::class, UserRegistrationApplicationService::class);

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
        
    }
}
