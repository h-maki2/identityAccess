<?php

namespace App\Providers;

use EloquentUnitOfWork;
use Illuminate\Support\ServiceProvider;
use packages\adapter\oauth\authToken\LaravelPassportAccessTokenDeactivationService;
use packages\adapter\oauth\authToken\LaravelPassportRefreshokenDeactivationService;
use packages\adapter\oauth\client\LaravelPassportClientFetcher;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\presenter\json\authentication\JsonLoginPresenter;
use packages\adapter\session\LaravelSessionAuthentication;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\SessionAuthentication;
use packages\domain\model\common\unitOfWork\UnitOfWork;
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

        // プレゼンテーション
        $this->app->bind(LoginOutputBoundary::class, JsonLoginPresenter::class);

        // Laravel
        $this->app->bind(SessionAuthentication::class, LaravelSessionAuthentication::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
