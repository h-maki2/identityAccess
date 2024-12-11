<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\application\authentication\login\LoginApplicationService;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\authenticationInformation\FailedLoginCount;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\LoginRestrictionStatus;
use packages\domain\model\authenticationInformation\NextLoginAllowedAt;
use packages\domain\model\authenticationInformation\SessionAuthentication;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\client\ClientDataForTest;
use packages\test\helpers\client\TestClientDataFactory;
use PHPUnit\Framework\TestCase;

class LoginApplicationServiceTest extends TestCase
{
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private IClientFetcher $clientFetcher;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthenticationService $authenticationService;
    private UserId $capturedUserId;
    private LoginResult $capturedLoginResult;
    private ClientDataForTest $expectedClientData;

    private const REDIRECT_URL = 'http://localhost:8080/callback';

    public function setUp(): void
    {
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);

        // markAsLoggedInメソッドが呼ばれた際に引数の値をキャプチャする
        $authenticationService = $this->createMock(AuthenticationService::class);
        $authenticationService
            ->method('markAsLoggedIn')
            ->with($this->callback(function (UserId $userId) {
                $this->capturedUserId = $userId;
                return true;
            }));
        $this->authenticationService = $authenticationService;

        // fetchByIdメソッドが呼ばれた際に返すデータを設定する
        $this->expectedClientData = TestClientDataFactory::create(
            redirectUri: new RedirectUrl(self::REDIRECT_URL)
        );
        $clientFetcher = $this->createMock(IClientFetcher::class);
        $clientFetcher->method('fetchById')->willReturn($this->expectedClientData);
        $this->clientFetcher = $clientFetcher;
    }

    public function test_メールアドレスとパスワードが正しい場合にログインができる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified,
            $userId
        );
        
        // when
        $inputedEmail = 'test@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher,
        );
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($result->loginSucceeded);
        // 認可コード取得用のURLが返されていることを確認する
        $expectedAuthorizationUrl = $this->expectedClientData->urlForObtainingAuthorizationCode(
            new RedirectUrl(self::REDIRECT_URL),
            $responseType,
            $state
        );
        $this->assertEquals($expectedAuthorizationUrl, $result->authorizationUrl);
        // 正しいuserIdでログインされていることを確認する
        $this->assertEquals($userId, $this->capturedUserId);
        // アカウントがロックされていないことを確認する
        $this->assertFalse($result->accountLocked);
    }

    public function test_メールアドレスが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformationTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        // 存在しないメールアドレスでログインを試みる
        $inputedEmail = 'mistake@example.com';
        $inputedPassword = 'ABCabc123_';
        $clientId = '1';
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher
        );
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($result->loginSucceeded);
        $this->assertEmpty($result->authorizationUrl);
    }

    public function test_パスワードが正しくない場合にログインが失敗する()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $this->authenticationInformationTestDataCreator->create(
            $email,
            $password,
            VerificationStatus::Verified
        );

        // when
        $inputedEmail = 'test@example.com';
        // パスワードが間違っている
        $inputedPassword = 'ABCabc123_!!jdn';
        $clientId = '1';
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher
        );
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($result->loginSucceeded);
        $this->assertEmpty($result->authorizationUrl);
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
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
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
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher
        );
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログインが失敗していることを確認する
        $this->assertFalse($result->loginSucceeded);
        $this->assertEmpty($result->authorizationUrl);
        // アカウントがロックされていることを確認する
        $this->assertTrue($result->accountLocked);
    }

    public function test_アカウントロックの有効期限外の場合、正しいメールアドレスとパスワードでログインできる()
    {
        // given
        // 認証情報を作成する
        $email = new UserEmail('test@example.com');
        $password = UserPassword::create('ABCabc123_');
        $userId = $this->authenticationInformationRepository->nextUserId();
        // アカウントがロックされているが再ログイン可能
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $this->authenticationInformationTestDataCreator->create(
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
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher
        );
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログインが成功していることを確認する
        $this->assertTrue($result->loginSucceeded);
        // 認可コード取得用のURLが返されていることを確認する
        $expectedAuthorizationUrl = $this->expectedClientData->urlForObtainingAuthorizationCode(
            new RedirectUrl(self::REDIRECT_URL),
            $responseType,
            $state
        );
        $this->assertEquals($expectedAuthorizationUrl, $result->authorizationUrl);
        // 正しいuserIdでログインされていることを確認する
        $this->assertEquals($userId, $this->capturedUserId);
        // アカウントがロックされていないことを確認する
        $this->assertFalse($result->accountLocked);
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
        $this->authenticationInformationTestDataCreator->create(
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
        $responseType = 'code';
        $state = 'abcdefg';
        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher
        );
        $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // ログイン失敗回数が更新されていることを確認する
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);
        $this->assertEquals(2, $authenticationInformation->loginRestriction()->failedLoginCount());
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
        $this->authenticationInformationTestDataCreator->create(
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
        $responseType = 'code';
        $state = 'abcdefg';

        $loginApplicationService = new LoginApplicationService(
            $this->authenticationInformationRepository,
            $this->authenticationService,
            $this->clientFetcher,
        );
        // 9回ログインに失敗する
        for ($i = 0; $i < 9; $i++) {
            $loginApplicationService->login(
                $inputedEmail, 
                $inputedPassword, 
                $clientId,
                self::REDIRECT_URL,
                $responseType,
                $state
            );
        }

        // when
        // 10回目のログインに失敗する
        $result = $loginApplicationService->login(
            $inputedEmail, 
            $inputedPassword, 
            $clientId,
            self::REDIRECT_URL,
            $responseType,
            $state
        );

        // then
        // アカウントがロックされていることを確認する
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);
        $this->assertEquals(LoginRestrictionStatus::Restricted->value, $authenticationInformation->loginRestriction()->loginRestrictionStatus());
        $this->assertNotNull($authenticationInformation->loginRestriction()->nextLoginAllowedAt());
        $this->assertEquals(10, $authenticationInformation->loginRestriction()->failedLoginCount());
        $this->assertTrue($result->accountLocked);
    }
}