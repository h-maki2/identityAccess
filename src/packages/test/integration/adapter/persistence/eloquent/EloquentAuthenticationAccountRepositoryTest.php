<?php

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\User as EloquentUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationConfirmationStatus;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use Tests\TestCase;

class EloquentAuthenticationAccountRepositoryTest extends TestCase
{
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);

        // テスト前にデータを全削除する
        EloquentUser::query()->delete();
        EloquentAuthenticationInformation::query()->delete();
    }

    public function test_認証アカウントをインサートできる()
    {
        // given
        // 認証アカウントを作成する
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('abcABC123!');
        $definitiveRegistrationConfirmationStatus = definitiveRegistrationConfirmationStatus::Verified;
        $userId = $this->authenticationAccountRepository->nextUserId();
        $loginRestriction = LoginRestriction::initialization();
        $unsubscribeStatus = UnsubscribeStatus::Subscribed;
        $authenticationAccount = TestAuthenticationAccountFactory::create(
            $userEmail,
            $userPassword,
            $definitiveRegistrationConfirmationStatus,
            $userId,
            $loginRestriction,
            $unsubscribeStatus
        );

        // when
        // 認証アカウントをインサートする
        $this->authenticationAccountRepository->save($authenticationAccount);

        // then
        // 保存したデータを取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $this->assertEquals($authenticationAccount, $actualAuthenticationAccount);
    }

    public function test_認証アカウントを更新できる()
    {
        // given
        // 本登録済みの認証アカウントを作成して保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $userPassword = UserPassword::create('abcABC123!');
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            password: $userPassword,
            definitiveRegistrationConfirmationStatus: definitiveRegistrationConfirmationStatus::Verified
        );

        // when
        // パスワードを変更して保存する
        $authenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $newPassword = UserPassword::create('newPassword123!');
        $authenticationAccount->changePassword($newPassword, new DateTimeImmutable());
        $this->authenticationAccountRepository->save($authenticationAccount);

        // then
        // 変更した認証アカウントを取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $this->assertEquals($newPassword, $actualAuthenticationAccount->password());
    }

    public function test_メールアドレスから認証アカウントを取得できる()
    {
        // given
        $検索対象のメールアドレス = new UserEmail('test@example.com');
        $検索対象の認証アカウント = $this->authenticationAccountTestDataCreator->create(
            email: $検索対象のメールアドレス
        );

        // 検索対象ではない認証アカウントを作成しておく
        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail('test2@example.com')
        );
        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail('test3@example.com')
        );

        // when
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findByEmail($検索対象のメールアドレス);

        // then
        $this->assertEquals($検索対象の認証アカウント, $actualAuthenticationAccount);
    }

    public function test_ユーザーIDから認証アカウントを取得できる()
    {
        // given
        $検索対象のユーザーID = $this->authenticationAccountRepository->nextUserId();
        $検索対象の認証アカウント = $this->authenticationAccountTestDataCreator->create(
            id: $検索対象のユーザーID
        );

        // 検索対象ではない認証アカウントを作成しておく
        $this->authenticationAccountTestDataCreator->create(
            id: $this->authenticationAccountRepository->nextUserId()
        );
        $this->authenticationAccountTestDataCreator->create(
            id: $this->authenticationAccountRepository->nextUserId()
        );

        // when
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($検索対象のユーザーID, UnsubscribeStatus::Subscribed);

        // then
        $this->assertEquals($検索対象の認証アカウント, $actualAuthenticationAccount);
    }

    public function test_認証アカウントを削除できる()
    {
        // given
        $削除対象のユーザーID = $this->authenticationAccountRepository->nextUserId();
        $削除対象の認証アカウント = $this->authenticationAccountTestDataCreator->create(
            id: $削除対象のユーザーID
        );

        // 削除対象ではない認証アカウントを作成しておく
        $削除対象ではないユーザーID = $this->authenticationAccountRepository->nextUserId();
        $削除対象ではない認証アカウント = $this->authenticationAccountTestDataCreator->create(
            id: $削除対象ではないユーザーID
        );



        // when
        $削除対象の認証アカウント->updateUnsubscribed();
        $this->authenticationAccountRepository->delete($削除対象の認証アカウント);

        // then
        // 削除した認証アカウントを取得できないことを確認する
        $this->assertNull($this->authenticationAccountRepository->findById($削除対象のユーザーID, UnsubscribeStatus::Subscribed));

        // 削除対象ではない認証アカウントは取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($削除対象ではないユーザーID, UnsubscribeStatus::Subscribed);
        $this->assertEquals($削除対象ではない認証アカウント, $actualAuthenticationAccount);
    }
}