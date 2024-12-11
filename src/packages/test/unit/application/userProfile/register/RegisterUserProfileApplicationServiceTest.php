<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\application\userProfile\register\RegisterUserProfileApplicationService;
use packages\application\userProfile\register\RegisterUserProfileOutputBoundary;
use packages\application\userProfile\register\RegisterUserProfileResult;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\userProfile\UserName;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\userProfile\UserProfileTestDataCreator;
use PHPUnit\Framework\TestCase;

class RegisterUserProfileApplicationServiceTest extends TestCase
{
    private InMemoryUserProfileRepository $userProfileRepository;
    private RegisterUserProfileApplicationService $registerUserProfileApplicationService;
    private RegisterUserProfileResult $registerUserProfileResult;

    public function setUp(): void
    {
        $inMemoryAuthenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($inMemoryAuthenticationInformationRepository);
        $authInfo = $authenticationInformationTestDataCreator->create();

        // loggedInUserIdメソッドをモック化する
        $authenticationService = $this->createMock(AuthenticationService::class);
        $authenticationService->method('loggedInUserId')->willReturn($authInfo->id());

        $this->userProfileRepository = new InMemoryUserProfileRepository();
        $this->registerUserProfileApplicationService = new RegisterUserProfileApplicationService(
            $this->userProfileRepository,
            $authenticationService
        );
    }

    public function test_ユーザープロフィールの登録が完了する()
    {
        // given
        $userNameString = 'test_user_name';
        $selfIntroductionTextString = '自己紹介文';

        // when
        $result = $this->registerUserProfileApplicationService->register($userNameString, $selfIntroductionTextString);

        // then
        // ユーザープロフィールが登録されていることを確認
        $userName = new UserName($userNameString);
        $actualUserProfile = $this->userProfileRepository->findByUserName($userName);
        $this->assertEquals($selfIntroductionTextString, $actualUserProfile->selfIntroductionText()->value);

        // バリデーションエラーが発生していないことを確認
        $this->assertTrue($result->isSuccess);
        $this->assertEmpty($result->validationErrorMessageList);
    }

    public function test_ユーザープロフィール登録の際にバリデーションエラーが発生する()
    {
        // given
        // ユーザー名が空文字列の場合
        $userNameString = '';
        // 自己紹介文が500文字以上の場合
        $selfIntroductionTextString = str_repeat('あ', 501);

        // when
        $result = $this->registerUserProfileApplicationService->register($userNameString, $selfIntroductionTextString);

        // then バリデーションエラーが発生していることを確認する
        $this->assertFalse($result->isSuccess);
        $expectedErrorMessageDataList = [
            new ValidationErrorMessageData('userName', ['ユーザー名は1文字以上50文字以内で入力してください。']),
            new ValidationErrorMessageData('selfIntroductionText', [
                '自己紹介文は500文字以内で入力してください。'
            ])
        ];
        $this->assertEquals($expectedErrorMessageDataList, $result->validationErrorMessageList);
    }
}