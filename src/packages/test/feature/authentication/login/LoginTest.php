<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\oauth\client\LaravelPassportClientFetcher;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\scope\Scope;
use packages\domain\model\oauth\scope\ScopeList;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAccessTokenCreator;
use packages\test\helpers\oauth\client\ClientTestDataCreator;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private EloquentAuthenticationAccountRepository $eloquentAuthenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private LaravelPassportClientFetcher $laravelPassportClientFetcher;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentAuthenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->eloquentAuthenticationAccountRepository);
        $this->laravelPassportClientFetcher = new LaravelPassportClientFetcher();
    }

    public function test_メールアドレスとパスワードが異なる場合に、ログインページにリダイレクトされる()
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
        // ログインページにリダイレクトされることを確認する
        $response->assertStatus(302);
    }

    public function test_正しいメールアドレスとパスワード場合に、認可コード取得画面にリダイレクトされる()
    {
        // 認証アカウントを作成する
        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail('test@example.com'),
            password: UserPassword::create('abcABC123!'),
            definitiveRegistrationCompletedStatus: DefinitiveRegistrationCompletedStatus::Completed,
            loginRestriction: LoginRestriction::initialization()
        );

        // クライアントを作成する
        $clientData = ClientTestDataCreator::create(
            redirectUrl: config('app.url') . '/auth/callback'
        );

        // when
        // 正しいメールアドレスとパスワードを入力してログインする
        $email = 'test@example.com';
        $password = 'abcABC123!';
        $scopeString = Scope::ReadAccount->value . ' ' . Scope::EditAccount->value . ' ' . Scope::DeleteAccount->value;
        $response = $this->post('/login', [
            'email' => $email,
            'password' => $password,
            'client_id' => $clientData->id,
            'redirect_url' => $clientData->redirect,
            'response_type' => 'code',
            'state' => 'abcdefg',
            'scope' => $scopeString
        ]);

        // then
        // 認可コード取得画面にリダイレクトされることを確認する
        $response->assertStatus(302);
        // 認可コード取得画面のURLを取得する
        $actualClient = $this->laravelPassportClientFetcher->fetchById(new ClientId($clientData->id));
        $actualScopeList = ScopeList::createFromString($scopeString);
        $response->assertRedirect($actualClient->urlForObtainingAuthorizationCode(
            new RedirectUrl($clientData->redirect),
            'code',
            'abcdefg',
            $actualScopeList
        ));
    }
}