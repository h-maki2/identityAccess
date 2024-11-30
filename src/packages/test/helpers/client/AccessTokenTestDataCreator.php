<?php

namespace packages\test\helpers\client;

use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\authenticationInformation\TestAuthenticationInformationFactory;
use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use packages\adapter\oauth\authToken\LaravelPassportAccessToken;

class AccessTokenTestDataCreator
{
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;

    public function __construct(EloquentAuthenticationInformationRepository $authenticationInformationRepository)
    {
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($authenticationInformationRepository);
    }

    /**
     * テスト用のアクセストークンを作成する
     */
    public function create(): LaravelPassportAccessToken
    {
        $authInfo = $this->authenticationInformationTestDataCreator->create();
        $eloquentAuthenticationInformation = EloquentAuthenticationInformation::find($authInfo->id()->value);
        return new LaravelPassportAccessToken($eloquentAuthenticationInformation->createToken('Test Token')->accessToken);
    }
}