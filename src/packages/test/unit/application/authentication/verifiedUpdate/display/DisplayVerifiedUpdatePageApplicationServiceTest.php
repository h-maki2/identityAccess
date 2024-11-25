<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageApplicationService;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageResult;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use PHPUnit\Framework\TestCase;

class DisplayVerifiedUpdatePageApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private DisplayVerifiedUpdatePageApplicationService $displayVerifiedUpdatePageApplicationService;
    private DisplayVerifiedUpdatePageOutputBoundary $outputBoundary;
    private DisplayVerifiedUpdatePageResult $capturedResult;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();

        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator(
            $this->authConfirmationRepository,
            $this->authenticationInformationRepository
        );

        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator(
            $this->authenticationInformationRepository
        );

        $outputBoundary = $this->createMock(DisplayVerifiedUpdatePageOutputBoundary::class);
        $outputBoundary
            ->method('present')
            ->with($this->callback(function (DisplayVerifiedUpdatePageResult $capturedResult) {
                $this->capturedResult = $capturedResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;

        $this->displayVerifiedUpdatePageApplicationService = new DisplayVerifiedUpdatePageApplicationService(
            $this->authConfirmationRepository,
            $this->outputBoundary
        );
    }

    public function test_有効なワンタイムトークンの場合に、認証済み更新ページを表示できる()
    {
        // given
        // 認証情報を作成して保存する
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // 有効な認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimeTokenExpiration = OneTimeTokenExpiration::create(new DateTimeImmutable('+1 day'));
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformation->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimeTokenExpiration: $oneTimeTokenExpiration,
            oneTimePassword: $oneTimePassword
        );

        // when
        $this->displayVerifiedUpdatePageApplicationService->displayVerifiedUpdatePage($oneTimeTokenValue->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($this->capturedResult->validationError);
        $this->assertEmpty($this->capturedResult->validationErrorMessage);

        // ワンタイムトークンとワンタイムパスワードが取得できることを確認
        $this->assertEquals($oneTimeTokenValue->value, $this->capturedResult->oneTimeTokenValue);
        $this->assertEquals($oneTimePassword->value, $this->capturedResult->oneTimePassword);
    }

    public function test_有効期限が切れている無効なワンタイムトークンの場合、認証済み更新ページを表示しない()
    {
        // given
        // 認証情報を作成して保存する
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // 有効期限が切れた認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'));
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformation->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimeTokenExpiration: $oneTimeTokenExpiration
        );

        // when
        $this->displayVerifiedUpdatePageApplicationService->displayVerifiedUpdatePage($oneTimeTokenValue->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($this->capturedResult->validationError);
        // バリデーションエラーメッセージが取得できることを確認
        $this->assertEquals('無効なワンタイムトークンです。', $this->capturedResult->validationErrorMessage);

        // ワンタイムトークンとワンタイムパスワードが取得できないことを確認
        $this->assertEmpty($this->capturedResult->oneTimeTokenValue);
        $this->assertEmpty($this->capturedResult->oneTimePassword);
    }
}