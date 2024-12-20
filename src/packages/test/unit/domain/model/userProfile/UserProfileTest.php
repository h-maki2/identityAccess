<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\service\userProfile\UserProfileService;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\userProfile\UserProfileTestDataCreator;
use PHPUnit\Framework\TestCase;

class UserProfileTest extends TestCase
{
    private InMemoryUserProfileRepository $userProfileRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private UserProfileTestDataCreator $userProfileTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private UserProfileService $userProfileService;

    public function setUp(): void
    {
        $this->userProfileRepository = new InMemoryUserProfileRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->userProfileTestDataCreator = new UserProfileTestDataCreator($this->userProfileRepository, $this->authenticationAccountRepository);
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->userProfileService = new UserProfileService($this->userProfileRepository);
    }

    public function test_ユーサー名が既に登録されていない場合に認証アカウントを作成できる()
    {
        // given
        // 認証アカウントを作成して保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // when
        $profileId = $this->userProfileRepository->nextUserProfileId();
        $userName = new UserName('user_name');
        $selfIntroductionText = new SelfIntroductionText('self_introduction_text');
        // 認証アカウントを作成する
        $userProfile = UserProfile::create(
            $authInfo->id(),
            $profileId,
            $userName,
            $selfIntroductionText,
            $this->userProfileService
        );

        // then
        // 入力した値が取得できることを確認する
        $this->assertEquals($authInfo->id(), $userProfile->userId());
        $this->assertEquals($profileId, $userProfile->profileId());
        $this->assertEquals($userName, $userProfile->name());
        $this->assertEquals($selfIntroductionText, $userProfile->selfIntroductionText());
    }

    public function test_既に登録されているユーザー名で認証アカウントを作成すると例外が発生する()
    {
        // given
        // 認証アカウントを作成して保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // 認証アカウントを作成して保存する
        $userName = new UserName('user_name');
        $this->userProfileTestDataCreator->create(userId: $authInfo->id(), userName: $userName);

        // when・then
        $profileId = $this->userProfileRepository->nextUserProfileId();
        $selfIntroductionText = new SelfIntroductionText('self_introduction_text');
        // 既に登録されているユーザー名で認証アカウントを作成すると例外が発生することを確認する
        $this->expectException(DomainException::class);
        UserProfile::create(
            $authInfo->id(),
            $profileId,
            $userName,
            $selfIntroductionText,
            $this->userProfileService
        );
    }

    public function test_自己紹介文を変更できる()
    {
        // given
        // 認証アカウントを作成して保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // 認証アカウントを作成して保存する
        $selfIntroductionText = new SelfIntroductionText('自己紹介文');
        $userProfile = $this->userProfileTestDataCreator->create(userId: $authInfo->id(), selfIntroductionText: $selfIntroductionText);

        // when
        // 自己紹介文を変更する
        $newSelfIntroductionText = new SelfIntroductionText('新しい自己紹介文');
        $userProfile->changeSelfIntroductionText($newSelfIntroductionText);

        // then
        // 自己紹介文が変更されていることを確認する
        $this->assertEquals($newSelfIntroductionText, $userProfile->selfIntroductionText());
    }

    public function test_既に存在するユーザー名ではない場合に、ユーザー名を変更できる()
    {
        // given
        // 認証アカウントを作成して保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // 認証アカウントを作成して保存する
        $userName = new UserName('user_name');
        $userProfile = $this->userProfileTestDataCreator->create(userId: $authInfo->id(), userName: $userName);

        // when
        // ユーザー名を変更する
        // 既に存在するユーザー名ではない場合
        $newUserName = new UserName('new_user_name');
        $userProfile->changeName($newUserName, $this->userProfileService);

        // then
        // ユーザー名が変更されていることを確認する
        $this->assertEquals($newUserName, $userProfile->name());
    }

    public function test_既に存在するユーザー名に変更しようとすると例外が発生する()
    {
        // given
        // 認証アカウントを作成して保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // 認証アカウントを作成して保存する
        $userName = new UserName('user_name');
        $userProfile = $this->userProfileTestDataCreator->create(userId: $authInfo->id(), userName: $userName);

        // when・then
        // 既に存在するユーザー名に変更しようとすると例外が発生することを確認する
        $this->expectException(DomainException::class);
        $userProfile->changeName($userName , $this->userProfileService);
    }
}