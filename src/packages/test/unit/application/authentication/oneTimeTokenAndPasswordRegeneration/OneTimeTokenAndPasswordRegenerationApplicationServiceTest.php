<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationApplicationService;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationOutputBoundary;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationResult;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use PHPUnit\Framework\TestCase;

class OneTimeTokenAndPasswordRegenerationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private OneTimeTokenAndPasswordRegenerationApplicationService $oneTimeTokenAndPasswordRegenerationApplicationService;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private OneTimeTokenAndPasswordRegenerationResult $catchedResult;
    private OneTimeTokenAndPasswordRegenerationOutputBoundary $outputBoundary;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();

        $outputBoundary = $this->createMock(OneTimeTokenAndPasswordRegenerationOutputBoundary::class);
        $outputBoundary
            ->method('formatForResponse')
            ->with($this->callback(function (OneTimeTokenAndPasswordRegenerationResult $catchedResult) {
                $this->catchedResult = $catchedResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;

        $this->oneTimeTokenAndPasswordRegenerationApplicationService = new OneTimeTokenAndPasswordRegenerationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformationRepository,
            $this->outputBoundary
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
    }

    public function test_認証情報が認証済みではない場合、ワンタイムトークンとワンタイムパスワードの再生成ができる()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformation->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($this->catchedResult->validationError);
        $this->assertEmpty($this->catchedResult->validationErrorMessage);

        // ワンタイムトークンが再生成されていることを確認
        $this->assertNotEquals($oneTimeTokenValue->value, $this->catchedResult->oneTimeTokenValue);

        // ワンタイムパスワードが再生成されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($authenticationInformation->id());
        $this->assertNotEquals($oneTimePasword->value, $actualAuthConfirmation->oneTimePassword()->value);
    }

    public function test_入力されたメールアドレスに紐づく認証情報が存在しない場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            email: $userEmail
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformation->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $正しくないメールアドレス = 'other@example.com';
        $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($正しくないメールアドレス);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($this->catchedResult->validationError);
        $this->assertEquals('メールアドレスが登録されていません。', $this->catchedResult->validationErrorMessage);
        $this->assertEmpty($this->catchedResult->oneTimeTokenValue);
    }

    public function test_認証情報がすでに認証済みの場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Verified // 認証済み
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformation->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $result = $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($this->catchedResult->validationError);
        $this->assertEquals('既にアカウントが認証済みです。', $this->catchedResult->validationErrorMessage);
        $this->assertEmpty($this->catchedResult->oneTimeTokenValue);
    }

    public function test_認証情報に紐づく認証確認情報が存在しない場合は例外が発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // when・then
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('認証情報が存在しません。userId: ' . $authenticationInformation->id()->value);
        $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);
    }
}