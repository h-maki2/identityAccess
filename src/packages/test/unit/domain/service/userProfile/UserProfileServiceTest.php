<?php

use Lcobucci\JWT\Signer\Key\InMemory;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\service\userProfile\UserProfileService;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\userProfile\UserProfileTestDataCreator;
use PHPUnit\Framework\TestCase;

class UserProfileServiceTest extends TestCase
{
    private InMemoryUserProfileRepository $userProfileRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private UserProfileTestDataCreator $userProfileTestDataCreator;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private UserProfileService $userProfileService;

    public function setUp(): void
    {
        $this->userProfileRepository = new InMemoryUserProfileRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->userProfileTestDataCreator = new UserProfileTestDataCreator($this->userProfileRepository, $this->authenticationInformationRepository);
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
        $this->userProfileService = new UserProfileService($this->userProfileRepository);
    }

    public function test_ユーザー名が既に登録されている場合を判定できる()
    {
        // given
        // あらかじめ認証情報を作成して保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(id: $userId);

        // ユーザープロフィールを作成して保存する
        $userName = new UserName('test user');
        $this->userProfileTestDataCreator->create(userId: $userId, userName: $userName);

        // when
        $result = $this->userProfileService->alreadyExistsUserName($userName);

        // then
        // 既に登録されているユーザー名であることを判定できる
        $this->assertTrue($result);
    }

    public function test_ユーザー名が登録されていない場合を判定できる()
    {
        // given
        $userName = new UserName('test user');

        // when
        $result = $this->userProfileService->alreadyExistsUserName($userName);

        // then
        // まだ登録されていないユーザー名であることを判定できる
        $this->assertFalse($result);
    }
}