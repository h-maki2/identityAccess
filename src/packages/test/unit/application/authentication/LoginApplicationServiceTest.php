<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\LoginApplicationService;
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
        $clientFetcher = $this->createMock(IClientFetcher::class);
        $clientFetcher->method('fetchById')->willReturn(TestClientDataFactory::create(
            null,
            null,
            'http://localhost:8080/callback'
        ));
        $this->clientFetcher = $clientFetcher;
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
            $this->clientFetcher
        );
        $loginResult = $loginApplicationService->login($inputedEmail, $inputedPassword, $clientId);

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($loginResult->loginSucceeded());
        // 認可コード取得用のURLが返されていることを確認する
        $this->assertEquals($clientData->urlForObtainingAuthorizationCode(), $loginResult->authorizationUrl());
        // アカウントがロックされていないことを確認する
        $this->assertFalse($loginResult->accountLocked());
    }
}