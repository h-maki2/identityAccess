<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\domain\model\oauth\scope\Scope;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
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
        // 認証アカウントを作成する
        $this->authenticationAccountTestDataCreator->create(
            new UserEmail('test@example.com'),
            UserPassword::create('abcABC123!'),
        );


        $存在しないメールアドレス = 'invalidEmail@example.com';
        $存在しないパスワード = 'abcABC123_';

        // クライアントを作成する
        $clientData = ClientTestDataCreator::create(
            redirectUrl: config('app.url') . '/auth/callback'
        );

        // when
        $scopeString = Scope::ReadAccount->value . ' ' . Scope::EditAccount->value . ' ' . Scope::DeleteAccount->value;
        $response = $this->post('/login', [
            'email' => $存在しないメールアドレス,
            'password' => $存在しないパスワード,
            'client_id' => $clientData->id,
            'redirect_url' => $clientData->redirect,
            'response_type' => 'code',
            'state' => 'abcdefg',
            'scope' => $scopeString
        ]);

        // then
        $response->assertStatus(302);
        $response->assertRedirect(config('app.url') . '/login');
    }
}