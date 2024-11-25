<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class EloquentAuthenticationInformaionRepositoryTest extends TestCase
{
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
    }

    public function test_認証情報をインサートできる()
    {
        // given
        // 認証情報を作成する
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('abcABC123!');
        $verificationStatus = VerificationStatus::Verified;
        $userId = $this->authenticationInformationRepository->nextUserId();
        $loginRestriction = LoginRestriction::initialization();
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            $userEmail,
            $userPassword,
            $verificationStatus,
            $userId,
            $loginRestriction
        );

        // when
        // 認証情報をインサートする
        $this->authenticationInformationRepository->save($authenticationInformation);

        // then
        // 保存したデータを取得できることを確認する
        $actualAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals($authenticationInformation, $actualAuthenticationInformation);
    }

    public function test_認証情報を更新できる()
    {
        // given
        // 認証済みの認証情報を作成して保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $userPassword = UserPassword::create('abcABC123!');
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            password: $userPassword,
            verificationStatus: VerificationStatus::Verified
        );

        // when
        // パスワードを変更して保存する
        $authenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $newPassword = UserPassword::create('newPassword123!');
        $authenticationInformation->changePassword($newPassword, new DateTimeImmutable());
        $this->authenticationInformationRepository->save($authenticationInformation);

        // then
        // 変更した認証情報を取得できることを確認する
        $actualAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals($newPassword, $actualAuthenticationInformation->password());
    }

    public function test_メールアドレスから認証情報を取得できる()
    {
        // given
        $検索対象のメールアドレス = new UserEmail('test@example.com');
        $検索対象の認証情報 = $this->authenticationInformationTestDataCreator->create(
            email: $検索対象のメールアドレス
        );

        // 検索対象ではない認証情報を作成しておく
        $this->authenticationInformationTestDataCreator->create(
            email: new UserEmail('test2@example.com')
        );
        $this->authenticationInformationTestDataCreator->create(
            email: new UserEmail('test3@example.com')
        );

        // when
        $actualAuthenticationInformation = $this->authenticationInformationRepository->findByEmail($検索対象のメールアドレス);

        // then
        $this->assertEquals($検索対象の認証情報, $actualAuthenticationInformation);
    }
}