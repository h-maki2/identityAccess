<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\application\changePassword\change\ChangePasswordApplicationService;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrlList;
use packages\domain\service\oauth\ClientService;
use packages\domain\service\oauth\ILoggedInUserIdFetcher;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\oauth\ClientTestDataCreator;
use packages\test\helpers\oauth\InMemoryClientFetcher;
use packages\test\helpers\oauth\TestClientDataFactory;
use PHPUnit\Framework\TestCase;

class ChangePasswordApplicationServiceTest extends TestCase
{
    private InMemoryClientFetcher $clientFetcher;
    private AuthenticationAccountTestDataCreator $authAccountTestDataCreator;
    private InMemoryAuthenticationAccountRepository $authAccountRepository;
    private ClientService $clientService;
    private TestClientDataFactory $testClientDataFactory;

    public function setUp(): void
    {
        $this->clientFetcher = new InMemoryClientFetcher();
        $this->authAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->authAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authAccountRepository);
        $this->clientService = new ClientService($this->clientFetcher);
        $this->testClientDataFactory = new TestClientDataFactory();
    }

    public function test_パスワードを変更できる()
    {
        // given
        // 認証アカウントを作成する
        $変更前のパスワード = UserPassword::create('acbABC123!');
        $authAccount = $this->authAccountTestDataCreator->create(password: $変更前のパスワード);

        // ログイン済みのuserIdを取得する処理をモック化する
        $loggedInUserIdFetcher = $this->createMock(ILoggedInUserIdFetcher::class);
        $loggedInUserIdFetcher->method('fetch')->willReturn($authAccount->id());

        // oauthのクライアントを作成する
        $clientId = new ClientId('1');
        $redirectUrl = 'http://localhost:8080/callback';
        $redirectUrlList = new RedirectUrlList($redirectUrl);
        $testClient = $this->testClientDataFactory->create(
            clientId: $clientId,
            redirectUriList: $redirectUrlList
        );

        // テスト用のクライアントデータをセットする
        $this->clientFetcher->setTestClientData($testClient);

        // パスワード変更のアプリケーションサービスを作成する
        $changePasswordApplicationService = new ChangePasswordApplicationService(
            $this->authAccountRepository,
            $this->clientFetcher,
            $loggedInUserIdFetcher
        );

        // when
        $変更後のパスワード = 'acbABC1234_';
        $result = $changePasswordApplicationService->changePassword(
            'edit_account',
            $変更後のパスワード,
            $clientId->value,
            $redirectUrl
        );

        // then
        // バリデーションエラーが発生していないことを確認する
        $this->assertFalse($result->isValidationError);
        $this->assertEmpty($result->validationErrorMessageList);

        // パスワード変更後のリダイレクトURLを取得できることを確認する
        $this->assertEquals($redirectUrl, $result->redirectUrl);

        // パスワードが変更されていることを確認する
        $authAccount = $this->authAccountRepository->findById($authAccount->id(), UnsubscribeStatus::Subscribed);
        $this->assertTrue($authAccount->password()->equals($変更後のパスワード));
    }
}