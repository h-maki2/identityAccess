<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationApplicationService;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataCreator;
use PHPUnit\Framework\TestCase;

class OneTimeTokenAndPasswordRegenerationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private OneTimeTokenAndPasswordRegenerationApplicationService $oneTimeTokenAndPasswordRegenerationApplicationService;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformaionTestDataCreator $authenticationInformaionTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->oneTimeTokenAndPasswordRegenerationApplicationService = new OneTimeTokenAndPasswordRegenerationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformaionRepository
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformaionRepository);
        $this->authenticationInformaionTestDataCreator = new AuthenticationInformaionTestDataCreator($this->authenticationInformaionRepository);
    }

    public function test_認証情報が認証済みではない場合、ワンタイムトークンとワンタイムパスワードの再生成ができる()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformaion = $this->authenticationInformaionTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformaion->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $result = $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessage);

        // ワンタイムトークンが再生成されていることを確認
        $this->assertNotEquals($oneTimeTokenValue->value, $result->oneTimeTokenValue);

        // ワンタイムパスワードが再生成されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($authenticationInformaion->id());
        $this->assertNotEquals($oneTimePasword->value, $actualAuthConfirmation->oneTimePassword()->value);
    }

    public function test_入力されたメールアドレスに紐づく認証情報が存在しない場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $this->authenticationInformaionTestDataCreator->create(
            email: $userEmail
        );

        // when
        $正しくないメールアドレス = 'other@example.com';
        $result = $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($正しくないメールアドレス);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('メールアドレスが登録されていません。', $result->validationErrorMessage);
        $this->assertEmpty($result->oneTimeTokenValue);
    }

    public function test_認証情報がすでに認証済みの場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $this->authenticationInformaionTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Verified // 認証済み
        );

        // when
        $result = $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('既にアカウントが認証済みです。', $result->validationErrorMessage);
        $this->assertEmpty($result->oneTimeTokenValue);
    }

    public function test_認証情報に紐づく認証確認情報が存在しない場合は例外が発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformaion = $this->authenticationInformaionTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // when・then
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('認証情報が存在しません。userId: ' . $authenticationInformaion->id()->value);
        $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);
    }
}