<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\adapter\persistence\inMemory\InMemoryUserProfileRepository;
use packages\application\userProfile\register\RegisterUserProfileApplicationService;
use packages\application\userProfile\register\RegisterUserProfileOutputBoundary;
use packages\application\userProfile\register\RegisterUserProfileResult;
use packages\domain\model\authenticationInformation\SessionAuthentication;
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

        // getUserIdメソッドをモック化する
        $sessionAuthentication = $this->createMock(SessionAuthentication::class);
        $sessionAuthentication->method('getUserId')->willReturn($authInfo->id());

        // formatForResponseメソッドが呼ばれた際に引数の値をキャプチャする
        $outputBoundary = $this->createMock(RegisterUserProfileOutputBoundary::class);
        $outputBoundary
            ->method('formatForResponse')
            ->with($this->callback(function (RegisterUserProfileResult $registerUserProfileResult) {
                $this->registerUserProfileResult = $registerUserProfileResult;
                return true;
            }));

        $this->userProfileRepository = new InMemoryUserProfileRepository();
        $this->registerUserProfileApplicationService = new RegisterUserProfileApplicationService(
            $this->userProfileRepository,
            $sessionAuthentication,
            $outputBoundary
        );
    }
}