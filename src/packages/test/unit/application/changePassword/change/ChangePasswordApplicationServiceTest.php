<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\application\changePassword\ChangePasswordApplicationService;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\RedirectUrlList;
use packages\domain\service\oauth\ClientService;
use packages\domain\service\oauth\LoggedInUserIdFetcher;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\oauth\client\ClientTestDataCreator;
use packages\test\helpers\oauth\client\InMemoryClientFetcher;
use packages\test\helpers\oauth\client\TestClientDataFactory;
use PHPUnit\Framework\TestCase;

class ChangePasswordApplicationServiceTest extends TestCase
{
    private InMemoryClientFetcher $clientFetcher;
    private AuthenticationAccountTestDataCreator $authAccountTestDataCreator;
    private InMemoryAuthenticationAccountRepository $authAccountRepository;

    public function setUp(): void
    {
        $this->clientFetcher = new InMemoryClientFetcher();
        $this->authAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->authAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authAccountRepository);
    }

    public function test_パスワードを変更できる()
    {
        // given
        // 認証アカウントを作成する
        $変更前のパスワード = UserPassword::create('acbABC123!');
        $authAccount = $this->authAccountTestDataCreator->create(password: $変更前のパスワード);

        // ログイン済みのuserIdを取得する処理をモック化する
        $loggedInUserIdFetcher = $this->createMock(LoggedInUserIdFetcher::class);
        $loggedInUserIdFetcher->method('fetch')->willReturn($authAccount->id());

        // パスワード変更のアプリケーションサービスを作成する
        $changePasswordApplicationService = new ChangePasswordApplicationService(
            $this->authAccountRepository,
            $loggedInUserIdFetcher
        );

        // when
        $変更後のパスワード = 'acbABC1234_';
        $result = $changePasswordApplicationService->changePassword(
            'edit_account',
            $変更後のパスワード
        );

        // then
        // バリデーションエラーが発生していないことを確認する
        $this->assertFalse($result->isValidationError);
        $this->assertEmpty($result->validationErrorMessageList);

        // パスワードが変更されていることを確認する
        $authAccount = $this->authAccountRepository->findById($authAccount->id(), UnsubscribeStatus::Subscribed);
        $this->assertTrue($authAccount->password()->equals($変更後のパスワード));
    }

    public function test_変更後のパスワードが適切な形式ではない場合に、バリデーションエラーが発生する()
    {
        // given
        // 認証アカウントを作成する
        $変更前のパスワード = 'acbABC123!';
        $authAccount = $this->authAccountTestDataCreator->create(
            password: UserPassword::create($変更前のパスワード)
        );

        // ログイン済みのuserIdを取得する処理をモック化する
        $loggedInUserIdFetcher = $this->createMock(LoggedInUserIdFetcher::class);
        $loggedInUserIdFetcher->method('fetch')->willReturn($authAccount->id());

        // パスワード変更のアプリケーションサービスを作成する
        $changePasswordApplicationService = new ChangePasswordApplicationService(
            $this->authAccountRepository,
            $loggedInUserIdFetcher
        );

        // when
        // 適切な形式ではないパスワードを設定する
        $変更後のパスワード = 'acb';
        $result = $changePasswordApplicationService->changePassword(
            'edit_account',
            $変更後のパスワード
        );

        // then
        // バリデーションエラーが発生していることを確認する
        $this->assertTrue($result->isValidationError);
        $expectedValidationErrorMessageList = [
            'パスワードは8文字以上で入力してください',
            'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
        ];
        $this->assertEquals($expectedValidationErrorMessageList, $result->validationErrorMessageList);

        // パスワードが変更されていないことを確認する
        $authAccount = $this->authAccountRepository->findById($authAccount->id(), UnsubscribeStatus::Subscribed);
        $this->assertTrue($authAccount->password()->equals($変更前のパスワード));
    }

    public function test_認証アカウントに紐づいていない不正なuserIdの場合に例外は発生する()
    {
        // given
        $不正なuserId = $this->authAccountRepository->nextUserId();

        // ログイン済みのuserIdを取得する処理をモック化する
        $loggedInUserIdFetcher = $this->createMock(LoggedInUserIdFetcher::class);
        $loggedInUserIdFetcher->method('fetch')->willReturn($不正なuserId);

        // パスワード変更のアプリケーションサービスを作成する
        $changePasswordApplicationService = new ChangePasswordApplicationService(
            $this->authAccountRepository,
            $loggedInUserIdFetcher
        );

        // when・then
        // 認証アカウントに紐づいていない不正なuserIdの場合に例外が発生することを確認する
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ユーザーが見つかりません');
        $changePasswordApplicationService->changePassword(
            'edit_account',
            'acbABC1234_'
        );
    }
}