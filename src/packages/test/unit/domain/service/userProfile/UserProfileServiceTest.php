<?php

use Lcobucci\JWT\Signer\Key\InMemory;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\service\userProfile\UserProfileService;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\userProfile\UserProfileTestDataCreator;
use PHPUnit\Framework\TestCase;

class UserProfileServiceTest extends TestCase
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

    public function test_ユーザー名が既に登録されている場合を判定できる()
    {
        // given
        // あらかじめ認証情報を作成して保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(id: $userId);

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