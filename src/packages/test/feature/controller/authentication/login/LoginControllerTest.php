<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\test\helpers\authenticationAccount\authenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAccessTokenCreator;
use packages\test\helpers\client\ClientTestDataCreator;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    private EloquentAuthenticationAccountRepository $eloquentAuthenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentAuthenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->eloquentAuthenticationAccountRepository);
    }

    public function test_メールアドレスとパスワードが異なる場合にログインに失敗する()
    {
        // given
        $存在しないメールアドレス = 'test@example.com';
        $存在しないパスワード = 'abcABC123!';

        // クライアントを作成する
        $clientData = ClientTestDataCreator::create(
            redirectUrl: config('app.url') . '/auth/callback'
        );

        // when
        $response = $this->withHeaders([
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api/login', [
            'email' => $存在しないメールアドレス,
            'password' => $存在しないパスワード,
            'client_id' => $clientData->id,
            'redirect_url' => $clientData->redirect,
            'response_type' => 'code',
            'state' => 'abcdefg'
        ]);

        // then
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'Bad Request',
                'details' => [
                    'accountLocked' => false
                ]
            ]
        ]);
    }

    public function test_メールアドレスの形式が不正な場合に400エラーが発生する()
    {
        // given
        $不正なメールアドレス = '不正なメールアドレス';
        $存在しないパスワード = 'abcABC123!';

        // クライアントを作成する
        $clientData = ClientTestDataCreator::create(
            redirectUrl:  config('app.url') . '/auth/callback'
        );

        // when
        $response = $this->withHeaders([
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api//login', [
            'email' => $不正なメールアドレス,
            'password' => $存在しないパスワード,
            'client_id' => $clientData->id,
            'redirect_url' => $clientData->redirect,
            'response_type' => 'code',
            'state' => 'abcdefg'
        ]);

        // then
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'Bad Request',
                'details' => null
            ]
        ]);
    }

    public function test_メールアドレスとパスワードが正しい場合にログインが成功する()
    {
        // given
        // 認証情報を登録する
        $emailString = 'test@example.com';
        $passwordString = 'abcABC123!';

        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail($emailString),
            password: UserPassword::create($passwordString),
            verificationStatus: VerificationStatus::Verified // 確認済み
        );

        // クライアントを作成する
        $clientData = ClientTestDataCreator::create(
            redirectUrl: config('app.url') . '/auth/callback'
        );

        // when
        $state = 'abcdefg';
        // ログインする
        $response = $this->withHeaders([
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api//login', [
            'email' => $emailString,
            'password' => $passwordString,
            'client_id' => $clientData->id,
            'redirect_url' => $clientData->redirect,
            'response_type' => 'code',
            'state' => $state
        ]);

        // then
        $response->assertStatus(200);
        
        $expectedQueryParams = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientData->id,
            'redirect_uri' => $clientData->redirect,
            'state' => $state
        ]);
        $expectedAuthorizationUrl = config('app.url') . '/oauth/authorize?' . $expectedQueryParams;
        $response->assertJson([
            'success' => true,
            'data' => [
                'authorizationUrl' => $expectedAuthorizationUrl
            ]
        ]);
    }
}