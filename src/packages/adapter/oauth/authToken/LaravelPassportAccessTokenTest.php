<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\client\AccessTokenTestDataCreator;
use packages\test\helpers\client\ClientTestDataCreator;
use Tests\TestCase;

class LaravelPassportAccessTokenTest extends TestCase
{
    private AccessTokenTestDataCreator $accessTokenTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->accessTokenTestDataCreator = new AccessTokenTestDataCreator(new EloquentAuthenticationInformationRepository());
    }

    public function test_アクセストークンからデコードしたIDを取得できる()
    {
        // given
        $accessToken = $this->accessTokenTestDataCreator->create();

        // when
        $accessTokenId = $accessToken->id();

        // then
        $this->assertIsString($accessTokenId);
    }
}