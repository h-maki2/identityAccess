<?php

use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserEmail;
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
}