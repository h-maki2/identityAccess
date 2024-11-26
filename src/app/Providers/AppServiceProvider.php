<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\presenter\json\authentication\JsonLoginPresenter;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\common\unitOfWork\UnitOfWork;
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

        // サービス
        $this->app->bind(IClientFetcher::class, ::class);

        // ユニットオブワーク
        // $this->app->bind(UnitOfWork::class, EloquentUnitOfWork::class);

        // プレゼンテーション
        $this->app->bind(LoginOutputBoundary::class, JsonLoginPresenter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
