<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\LoginApplicationService;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\LoginRestrictionStatus;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\model\client\IClientFetcher;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataFactory;
use packages\test\helpers\client\TestClientDataFactory;
use PHPUnit\Framework\TestCase;

class LoginApplicationServiceTest extends TestCase
{
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IClientFetcher $clientFetcher;
    private AuthenticationInformaionTestDataFactory $authenticationInformaionTestDataFactory;

    public function setUp(): void
    {
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->authenticationInformaionRepository);
    }

    public function test_メールアドレスとパスワードが正しい場合にログインができる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataFactory->create(
            $email,
            $password,
            VerificationStatus::Verified,
            $userId
        );

        // markAsLoggedInメソッドが呼ばれた際に引数の値をキャプチャする
        $sessionAuthentication = $this->createMock(SessionAuthentication::class);
        $capturedUserId = null;
        $sessionAuthentication->expects($this->once())
            ->method('markAsLoggedIn')
            ->with($this->callback(function ($userId) use (&$capturedUserId) {
                $capturedUserId = $userId;
                return true;
            }));

        // fetchByIdメソッドが呼ばれた際に返すデータを設定する
        $clientData = TestClientDataFactory::create(
            null,
            null,
            'http://localhost:8080/callback'
        );
        $clientFetcher = $this->createMock(IClientFetcher::class);
        $clientFetcher->method('fetchById')->willReturn($clientData);
        
        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $sessionAuthentication,
            $clientFetcher
        );
        $loginResult = $loginApplicationService->login($inputedEmail, $inputedPassword, $clientId);

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($loginResult->loginSucceeded());
        // 認可コード取得用のURLが返されていることを確認する
        $this->assertEquals($clientData->urlForObtainingAuthorizationCode(), $loginResult->authorizationUrl());
    }

    public function test_メールアドレスが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformaionTestDataFactory->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        // 存在しないメールアドレスでログインを試みる
        $inputedEmail = 'mistake@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->createMock(SessionAuthentication::class),
            $this->createMock(IClientFetcher::class)
        );
        $loginResult = $loginApplicationService->login($inputedEmail, $inputedPassword, $clientId);

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($loginResult->loginSucceeded());
        $this->assertEmpty($loginResult->authorizationUrl());
    }

    public function test_パスワードが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformaionTestDataFactory->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        $inputedEmail = 'test@example.com';
        // パスワードが間違っている
        $inputedPassword = 'ABCabc123_!';
        $clientId = '1';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->createMock(SessionAuthentication::class),
            $this->createMock(IClientFetcher::class)
        );
        $loginResult = $loginApplicationService->login($inputedEmail, $inputedPassword, $clientId);

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($loginResult->loginSucceeded());
        $this->assertEmpty($loginResult->authorizationUrl());
    }

    public function test_アカウントがロックされている場合はログインが失敗する()
    {
        // given
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        // アカウントがロックされていて再ログインも不可
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );
        $this->authenticationInformaionTestDataFactory->create(
            $email,
            $password,
            VerificationStatus::Verified,
            null,
            $loginRestriction
        );

        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_!';
        $clientId = '1';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->createMock(SessionAuthentication::class),
            $this->createMock(IClientFetcher::class)
        );
        $loginResult = $loginApplicationService->login($inputedEmail, $inputedPassword, $clientId);

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($loginResult->loginSucceeded());
        $this->assertEmpty($loginResult->authorizationUrl());
    }

    public function test_アカウントロックの有効期限外の場合、正しいメールアドレスとパスワードでログインできる()
    {

    }

    public function test_ログインに失敗した場合、ログイン失敗回数が更新される()
    {

    }

    public function test_ログインに失敗した場合、失敗回数が一定回数を超えた場合アカウントがロックされる()
    {

    }
}