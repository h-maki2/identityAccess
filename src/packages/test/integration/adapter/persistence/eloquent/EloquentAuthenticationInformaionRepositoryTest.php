<?php

use App\Models\authenticationAccount as EloquentAuthenticationAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\test\helpers\authenticationAccount\authenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use Tests\TestCase;

class EloquentAuthenticationInformaionRepositoryTest extends TestCase
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
        EloquentAuthenticationAccount::query()->delete();
    }

    public function test_認証情報をインサートできる()
    {
        // given
        // 認証情報を作成する
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('abcABC123!');
        $verificationStatus = VerificationStatus::Verified;
        $userId = $this->authenticationAccountRepository->nextUserId();
        $loginRestriction = LoginRestriction::initialization();
        $authenticationAccount = TestAuthenticationAccountFactory::create(
            $userEmail,
            $userPassword,
            $verificationStatus,
            $userId,
            $loginRestriction
        );

        // when
        // 認証情報をインサートする
        $this->authenticationAccountRepository->save($authenticationAccount);

        // then
        // 保存したデータを取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($userId);
        $this->assertEquals($authenticationAccount, $actualAuthenticationAccount);
    }

    public function test_認証情報を更新できる()
    {
        // given
        // 認証済みの認証情報を作成して保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $userPassword = UserPassword::create('abcABC123!');
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            password: $userPassword,
            verificationStatus: VerificationStatus::Verified
        );

        // when
        // パスワードを変更して保存する
        $authenticationAccount = $this->authenticationAccountRepository->findById($userId);
        $newPassword = UserPassword::create('newPassword123!');
        $authenticationAccount->changePassword($newPassword, new DateTimeImmutable());
        $this->authenticationAccountRepository->save($authenticationAccount);

        // then
        // 変更した認証情報を取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($userId);
        $this->assertEquals($newPassword, $actualAuthenticationAccount->password());
    }

    public function test_メールアドレスから認証情報を取得できる()
    {
        // given
        $検索対象のメールアドレス = new UserEmail('test@example.com');
        $検索対象の認証情報 = $this->authenticationAccountTestDataCreator->create(
            email: $検索対象のメールアドレス
        );

        // 検索対象ではない認証情報を作成しておく
        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail('test2@example.com')
        );
        $this->authenticationAccountTestDataCreator->create(
            email: new UserEmail('test3@example.com')
        );

        // when
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findByEmail($検索対象のメールアドレス);

        // then
        $this->assertEquals($検索対象の認証情報, $actualAuthenticationAccount);
    }

    public function test_ユーザーIDから認証情報を取得できる()
    {
        // given
        $検索対象のユーザーID = $this->authenticationAccountRepository->nextUserId();
        $検索対象の認証情報 = $this->authenticationAccountTestDataCreator->create(
            id: $検索対象のユーザーID
        );

        // 検索対象ではない認証情報を作成しておく
        $this->authenticationAccountTestDataCreator->create(
            id: $this->authenticationAccountRepository->nextUserId()
        );
        $this->authenticationAccountTestDataCreator->create(
            id: $this->authenticationAccountRepository->nextUserId()
        );

        // when
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($検索対象のユーザーID);

        // then
        $this->assertEquals($検索対象の認証情報, $actualAuthenticationAccount);
    }

    public function test_認証情報を削除できる()
    {
        // given
        $削除対象のユーザーID = $this->authenticationAccountRepository->nextUserId();
        $削除対象の認証情報 = $this->authenticationAccountTestDataCreator->create(
            id: $削除対象のユーザーID
        );

        // 削除対象ではない認証情報を作成しておく
        $削除対象ではないユーザーID = $this->authenticationAccountRepository->nextUserId();
        $削除対象ではない認証情報 = $this->authenticationAccountTestDataCreator->create(
            id: $削除対象ではないユーザーID
        );

        // when
        $this->authenticationAccountRepository->delete($削除対象のユーザーID);

        // then
        // 削除した認証情報を取得できないことを確認する
        $this->assertNull($this->authenticationAccountRepository->findById($削除対象のユーザーID));

        // 削除対象ではない認証情報は取得できることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findById($削除対象ではないユーザーID);
        $this->assertEquals($削除対象ではない認証情報, $actualAuthenticationAccount);
    }
}