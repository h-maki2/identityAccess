<?php

namespace packages\test\helpers\client;

use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\authenticationInformation\TestAuthenticationInformationFactory;
use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;

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
    public function create(): AccessToken
    {
        $authInfo = TestAuthenticationInformationFactory::create();
        $eloquentAuthenticationInformation = EloquentAuthenticationInformation::find($authInfo->id()->value);
        return new AccessToken($eloquentAuthenticationInformation->createToken('Test Token')->accessToken);
    }
}