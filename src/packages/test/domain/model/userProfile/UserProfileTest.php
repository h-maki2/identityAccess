<?php

use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserPassword;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\VerificationStatus;
use packages\domain\service\userProfile\UserProfileService;
use packages\test\domain\model\userProfile\helper\TestUserProfileFactory;
use packages\test\domain\model\userProfile\helper\UserProfileTestDataFactory;
use PHPUnit\Framework\TestCase;

class UserProfileTest extends TestCase
{
    private IUserProfileRepository $userProfileRepository;

    public function setUp(): void
    {
        $this->userProfileRepository = new InMemoryUserProfileRepository();
    }

    public function test_重複したメールアドレスを持つユーザーが存在しない場合、ユーザープロフィールを初期化できる()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $userProfileTestDataFactory = new UserProfileTestDataFactory($this->userProfileRepository);
        $userProfileTestDataFactory->create($alreadyExistsUserEmail);

        $email = new UserEmail('otheruser@example.com');
        $userId = $this->userProfileRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $userProfileService = new UserProfileService($this->userProfileRepository);

        // when
        $userProfile = UserProfile::create(
            $userId,
            $email,
            $password,
            $userProfileService
        );

        // then
        $this->assertEquals('otheruser', $userProfile->name()->value);
        $this->assertEquals(VerificationStatus::Unverified, $userProfile->verificationStatus());

        // 以下の属性はそのまま設定される
        $this->assertEquals($email, $userProfile->email());
        $this->assertEquals($userId, $userProfile->id());
        $this->assertEquals($password, $userProfile->password());
    }

    public function test_重複したメールアドレスを持つユーザーが既に存在する場合、ユーザープロフィールを初期化できない()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $userProfileTestDataFactory = new UserProfileTestDataFactory($this->userProfileRepository);
        $userProfileTestDataFactory->create($alreadyExistsUserEmail);

        // メールアドレスが重複している
        $email = new UserEmail('user@example.com');
        $userId = $this->userProfileRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $userProfileService = new UserProfileService($this->userProfileRepository);

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('すでに存在するメールアドレスです。');
        UserProfile::create(
            $userId,
            $email,
            $password,
            $userProfileService
        );
    }

    public function test_ユーザープロフィールを再構築できる()
    {
        // given
        $email = new UserEmail('otheruser@example.com');
        $userId = $this->userProfileRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $verificationStatus = VerificationStatus::Verified;
        $userName = UserName::create('test user');

        // when
        $userProfile = UserProfile::reconstruct(
            $userId,
            $email,
            $userName,
            $password,
            $verificationStatus
        );

        // then
        $this->assertEquals($email, $userProfile->email());
        $this->assertEquals($userId, $userProfile->id());
        $this->assertEquals($password, $userProfile->password());
        $this->assertEquals($userName, $userProfile->name());
        $this->assertEquals($verificationStatus, $userProfile->verificationStatus());
    }

    public function 認証ステータスを認証済みに更新できる()
    {
        // given
        // 認証済みステータスが未認証のユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Unverified;
        $userProfile = TestUserProfileFactory::create(
            null,
            null,
            null,
            $verificationStatus
        );

        // when
        $userProfile->updateVerified();

        // then
        $this->assertEquals(VerificationStatus::Verified, $userProfile->verificationStatus());
    }

    public function test_認証ステータスが認証済みの場合、ユーザー名の変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $userName = UserName::create('test user');
        $userProfile = TestUserProfileFactory::create(
            null,
            $userName,
            null,
            $verificationStatus
        );

        // when
        $userNameAfterChange = UserName::create('test user after change');
        $userProfile->changeName($userNameAfterChange);

        // then
        $this->assertEquals($userNameAfterChange, $userProfile->name());
    }

    public function test_認証ステータスが未認証の場合、ユーザー名の変更が行えない()
    {
        // given
        // 認証済みステータスが未認証のユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Unverified;
        $userName = UserName::create('test user');
        $userProfile = TestUserProfileFactory::create(
            null,
            $userName,
            null,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $userNameAfterChange = UserName::create('test user after change');
        $userProfile->changeName($userNameAfterChange);
    }

    public function test_認証ステータスが認証済みの場合、パスワードの変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $password = UserPassword::create('124abcABC!');
        $userProfile = TestUserProfileFactory::create(
            null,
            null,
            $password,
            $verificationStatus
        );

        // when
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $userProfile->changePassword($passwordAfterChange);

        // then
        $this->assertEquals($passwordAfterChange, $userProfile->password());
    }

    public function test_認証ステータスが未認証の場合、パスワードの変更が行えない()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;
        $password = UserPassword::create('124abcABC!');
        $userProfile = TestUserProfileFactory::create(
            null,
            null,
            $password,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $userProfile->changePassword($passwordAfterChange);
    }
}