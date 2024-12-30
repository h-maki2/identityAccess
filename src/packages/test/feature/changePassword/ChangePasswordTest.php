<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\oauth\scope\ScopeList;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\oauth\AccessTokenTestDataCreator;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use DatabaseTransactions;

    private AuthenticationAccountTestDataCreator $authAccountTestDataCreator;
    private EloquentAuthenticationAccountRepository $authAccountRepository;
    private AccessTokenTestDataCreator $accessTokenTestDataCreator;

    public function setUp(): void
    {
        parent::setUp();
        $this->authAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authAccountRepository);
        $this->accessTokenTestDataCreator = new AccessTokenTestDataCreator();
    }

    public function test_パスワードの変更が成功する()
    {
        // given
        // 認証アカウントを作成する
        $変更前のパスワード = 'acbABC123!';
        $userId = $this->authAccountRepository->nextUserId();
        $authAccount = $this->authAccountTestDataCreator->create(
            id: $userId, 
            password: UserPassword::create($変更前のパスワード)
        );

        // アクセストークンを作成する
        $validScope = 'edit_account';
        $socpeList = ScopeList::createFromString($validScope);
        $accessToken = $this->accessTokenTestDataCreator->create(
            $socpeList,
            $authAccount
        );

        // when
        $変更後のパスワード = 'acbABC1234_';
        $response = $this->post('/api/password/change', [
            'scope' => $validScope,
            'password' => $変更後のパスワード
        ], [
            'Accept' => 'application/vnd.example.v1+json',
            'Authorization' => 'Bearer ' . $accessToken->value
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => []
        ]);
    }

    public function test_不正な形式のパスワードの場合に、パスワードの変更に失敗する()
    {
        // given
        // 認証アカウントを作成する
        $変更前のパスワード = 'acbABC123!';
        $userId = $this->authAccountRepository->nextUserId();
        $authAccount = $this->authAccountTestDataCreator->create(
            id: $userId, 
            password: UserPassword::create($変更前のパスワード)
        );

        // アクセストークンを作成する
        $validScope = 'edit_account';
        $socpeList = ScopeList::createFromString($validScope);
        $accessToken = $this->accessTokenTestDataCreator->create(
            $socpeList,
            $authAccount
        );

        // when
        $不正な形式のパスワード = 'pass';
        $response = $this->post('/api/password/change', [
            'scope' => $validScope,
            'password' => $不正な形式のパスワード
        ], [
            'Accept' => 'application/vnd.example.v1+json',
            'Authorization' => 'Bearer ' . $accessToken->value
        ]);

        // then
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'Bad Request',
                'details' => [
                    'パスワードは8文字以上で入力してください',
                    'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
                ],
            ]
        ]);
    }

    public function test_アクセストークンが不正な場合にパスワードを変更できない()
    {
        // when
        $変更後のパスワード = 'acbABC1234_';
        $不正なアクセストークン = 'invalid_access_token';
        $response = $this->post('/api/password/change', [
            'scope' => '',
            'password' => $変更後のパスワード
        ], [
            'Accept' => 'application/vnd.example.v1+json',
            'Authorization' => 'Bearer ' . $不正なアクセストークン
        ]);

        // then
        // 認証エラーが発生することを確認する
        $response->assertStatus(401);
    }
}