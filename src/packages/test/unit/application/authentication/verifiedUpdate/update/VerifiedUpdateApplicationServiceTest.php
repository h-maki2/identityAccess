<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateApplicationService;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\AuthenticationInformation\VerificationStatus;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\AuthenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class VerifiedUpdateApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private VerifiedUpdate $verifiedUpdate;
    private TestUnitOfWork $unitOfWork;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private VerifiedUpdateApplicationService $verifiedUpdateApplicationService;
    private VerifiedUpdateOutputBoundary $outputBoundary;
    private VerifiedUpdateResult $capturedResult;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->unitOfWork = new TestUnitOfWork();
        $this->verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);

        $outputBoundary = $this->createMock(VerifiedUpdateOutputBoundary::class);
        $outputBoundary
            ->method('present')
            ->with($this->callback(function (VerifiedUpdateResult $capturedResult) {
                $this->capturedResult = $capturedResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;

        $this->verifiedUpdateApplicationService = new VerifiedUpdateApplicationService(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork,
            $this->outputBoundary
        );
    }

    public function test_ワンタイムトークンとワンタイムパスワードが正しい場合に、認証情報を認証済みに更新できる()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 正しいワンタイムトークンとワンタイムパスワードを入力する
        $this->verifiedUpdateApplicationService->verifiedUpdate($oneTimeTokenValue->value, $oneTimePassword->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($this->capturedResult->validationError);
        $this->assertEmpty($this->capturedResult->validationErrorMessage);

        // 認証情報が認証済みになっていることを確認
        $updatedAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationInformation->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedAuthConfirmation = $this->authConfirmationRepository->findByToken($oneTimeTokenValue);
        $this->assertNull($deletedAuthConfirmation);
    }

    public function test_ワンタイムトークンが有効ではない場合、認証情報を認証済みに更新できない()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 存在しないワンタイムトークンを生成
        $invalidOneTimeTokenValue = OneTimeTokenValue::create();
        $result = $this->verifiedUpdateApplicationService->verifiedUpdate($invalidOneTimeTokenValue->value, $oneTimePassword->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($this->capturedResult->validationError);
        $this->assertEquals('ワンタイムトークンが無効です。', $this->capturedResult->validationErrorMessage);
    }

    public function test_ワンタイムパスワードが正しくない場合、認証情報を認証済みに更新できない()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 正しくないワンタイムパスワードを入力する
        $invalidOneTimePassword = OneTimePassword::reconstruct('654321');
        $result = $this->verifiedUpdateApplicationService->verifiedUpdate($oneTimeTokenValue->value, $invalidOneTimePassword->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($this->capturedResult->validationError);
        $this->assertEquals('ワンタイムトークンかワンタイムパスワードが無効です。', $this->capturedResult->validationErrorMessage);
    }
}