<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\test\helpers\authenticationAccount\authenticationAccountTestDataCreator;
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
        $this->accessTokenTestDataCreator = new AccessTokenTestDataCreator(new EloquentAuthenticationAccountRepository());
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