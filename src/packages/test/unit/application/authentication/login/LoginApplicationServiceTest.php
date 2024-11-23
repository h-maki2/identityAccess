<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\login\LoginApplicationService;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\LoginRestrictionStatus;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataCreator;
use packages\test\helpers\client\ClientDataForTest;
use packages\test\helpers\client\TestClientDataFactory;
use PHPUnit\Framework\TestCase;

class LoginApplicationServiceTest extends TestCase
{
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IClientFetcher $clientFetcher;
    private AuthenticationInformaionTestDataCreator $authenticationInformaionTestDataCreator;
    private SessionAuthentication $sessionAuthentication;
    private UserId $capturedUserId;
    private LoginOutputBoundary $outputBoundary;
    private LoginResult $capturedLoginResult;
    private ClientDataForTest $expectedClientData;

    public function setUp(): void
    {
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->authenticationInformaionTestDataCreator = new AuthenticationInformaionTestDataCreator($this->authenticationInformaionRepository);

        // markAsLoggedInメソッドが呼ばれた際に引数の値をキャプチャする
        $sessionAuthentication = $this->createMock(SessionAuthentication::class);
        $sessionAuthentication
            ->method('markAsLoggedIn')
            ->with($this->callback(function (UserId $userId) {
                $this->capturedUserId = $userId;
                return true;
            }));
        $this->sessionAuthentication = $sessionAuthentication;

        // fetchByIdメソッドが呼ばれた際に返すデータを設定する
        $this->expectedClientData = TestClientDataFactory::create(
            redirectUri: new RedirectUrl('http://localhost:8080/callback')
        );
        $clientFetcher = $this->createMock(IClientFetcher::class);
        $clientFetcher->method('fetchById')->willReturn($this->expectedClientData);
        $this->clientFetcher = $clientFetcher;

        // presentメソッドが呼ばれた際に引数の値をキャプチャする
        $outputBoundary = $this->createMock(LoginOutputBoundary::class);
        $outputBoundary
            ->method('present')
            ->with($this->callback(function (LoginResult $capturedLoginResult) {
                $this->capturedLoginResult = $capturedLoginResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;
    }

    public function test_メールアドレスとパスワードが正しい場合にログインができる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            $userId
        );
        
        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($this->capturedLoginResult->loginSucceeded);
        // 認可コード取得用のURLが返されていることを確認する
        $expectedAuthorizationUrl = $this->expectedClientData->urlForObtainingAuthorizationCode(
            new RedirectUrl($redirectUrl),
            $responseType
        );
        $this->assertEquals($expectedAuthorizationUrl, $this->capturedLoginResult->authorizationUrl);
        // 正しいuserIdでログインされていることを確認する
        $this->assertEquals($userId, $this->capturedUserId);
        // アカウントがロックされていないことを確認する
        $this->assertFalse($this->capturedLoginResult->accountLocked);
    }

    public function test_メールアドレスが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        // 存在しないメールアドレスでログインを試みる
        $inputedEmail = 'mistake@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($this->capturedLoginResult->loginSucceeded);
        $this->assertEmpty($this->capturedLoginResult->authorizationUrl);
    }

    public function test_パスワードが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        $inputedEmail = 'test@example.com';
        // パスワードが間違っている
        $inputedPassword = 'ABCabc123_!!jdn';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($this->capturedLoginResult->loginSucceeded);
        $this->assertEmpty($this->capturedLoginResult->authorizationUrl);
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
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            $userId,
            $loginRestriction
        );

        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($this->capturedLoginResult->loginSucceeded);
        $this->assertEmpty($this->capturedLoginResult->authorizationUrl);
        // アカウントがロックされていることを確認する
        $this->assertTrue($this->capturedLoginResult->accountLocked);
    }

    public function test_アカウントロックの有効期限外の場合、正しいメールアドレスとパスワードでログインできる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $userId = $this->authenticationInformaionRepository->nextUserId();
        // アカウントがロックされているが再ログイン可能
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            $userId,
            $loginRestriction
        );

        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($this->capturedLoginResult->loginSucceeded);
        // 認可コード取得用のURLが返されていることを確認する
        $expectedAuthorizationUrl = $this->expectedClientData->urlForObtainingAuthorizationCode(
            new RedirectUrl($redirectUrl),
            $responseType
        );
        $this->assertEquals($expectedAuthorizationUrl, $this->capturedLoginResult->authorizationUrl);
        // 正しいuserIdでログインされていることを確認する
        $this->assertEquals($userId, $this->capturedUserId);
        // アカウントがロックされていないことを確認する
        $this->assertFalse($this->capturedLoginResult->accountLocked);
    }

    public function test_ログインに失敗した場合、ログイン失敗回数が更新される()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(1),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            null,
            $loginRestriction
        );

        // when
        $inputedEmail = 'test@example.com';
        // 不正なパスワード
        $inputedPassword = 'ABCabc123_!!!';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // ログイン失敗回数が更新されていることを確認する
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);
        $this->assertEquals(2, $authenticationInformaion->loginRestriction()->failedLoginCount());
    }

    public function test_ログインに失敗した場合、失敗回数が一定回数を超えた場合アカウントがロックされる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $this->authenticationInformaionTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            null,
            $loginRestriction
        );

        // when
        $inputedEmail = 'test@example.com';
        // パスワードが不正
        $inputedPassword = 'ABCabc123_!!';
        $clientId = '1';
        $redirectUrl = 'http://localhost:8080/callback';
        $responseType = 'code';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformaionRepository,
            $this->sessionAuthentication,
            $this->clientFetcher,
            $this->outputBoundary
        );
        // 9回ログインに失敗する
        for ($i = 0; $i < 9; $i++) {
            $loginApplicationService->login(
                $inputedEmail, 
                $inputedPassword, 
                $clientId,
                $redirectUrl,
                $responseType
            );
        }

        // when
        // 10回目のログインに失敗する
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            $redirectUrl,
            $responseType
        );

        // then
        // アカウントがロックされていることを確認する
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);
        $this->assertEquals(LoginRestrictionStatus::Restricted->value, $authenticationInformaion->loginRestriction()->loginRestrictionStatus());
        $this->assertNotNull($authenticationInformaion->loginRestriction()->nextLoginAllowedAt());
        $this->assertEquals(10, $authenticationInformaion->loginRestriction()->failedLoginCount());
        $this->assertTrue($this->capturedLoginResult->accountLocked);
    }
}