<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\client\AccessTokenTestDataCreator;
use Tests\TestCase;

class RegisterUserProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    private AccessTokenTestDataCreator $accessTokenTestDataCreator;

    public function setUp(): void
    {
        parent::setUp();
        $this->accessTokenTestDataCreator = new AccessTokenTestDataCreator(
            new EloquentAuthenticationInformationRepository()
        );
    }

    public function test_ユーザー名と自己紹介文が有効な場合に、ユーザープロフィールを登録できる()
    {
        // given
        $accessToken = $this->accessTokenTestDataCreator->create();

        // when
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken->value,
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api/userProfile/register', [
            'userName' => 'テストユーザー',
            'selfIntroductionText' => 'こんにちは'
        ]);

        // then ユーザープロフィールの登録が成功していることを確認する
        $response->assertStatus(200);
    }

    public function test_無効なアクセストークンの場合に、ユーザープロフィールを登録できない()
    {
        // given 無効なアクセストークンを作成する
        $accessToken = $this->accessTokenTestDataCreator->create();
        $invalidAccessToken = $accessToken->value . 'invalid';

        // when
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $invalidAccessToken,
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api/userProfile/register', [
            'userName' => 'テストユーザー',
            'selfIntroductionText' => 'こんにちは'
        ]);

        // then 認証エラーが返されることを確認する
        $response->assertStatus(401);
    }
}