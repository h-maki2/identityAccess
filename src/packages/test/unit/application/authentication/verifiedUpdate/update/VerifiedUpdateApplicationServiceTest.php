<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateApplicationService;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataCreator;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class VerifiedUpdateApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private VerifiedUpdate $verifiedUpdate;
    private TestUnitOfWork $unitOfWork;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformaionTestDataCreator $authenticationInformaionTestDataCreator;
    private VerifiedUpdateApplicationService $verifiedUpdateApplicationService;
    private VerifiedUpdateOutputBoundary $outputBoundary;
    private VerifiedUpdateResult $capturedResult;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->unitOfWork = new TestUnitOfWork();
        $this->verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformaionRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformaionRepository);
        $this->authenticationInformaionTestDataCreator = new AuthenticationInformaionTestDataCreator($this->authenticationInformaionRepository);

        $outputBoundary = $this->createMock(VerifiedUpdateOutputBoundary::class);
        $outputBoundary
            ->method('present')
            ->with($this->callback(function (VerifiedUpdateResult $capturedResult) {
                $this->capturedResult = $capturedResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;

        $this->verifiedUpdateApplicationService = new VerifiedUpdateApplicationService(
            $this->authenticationInformaionRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork,
            $this->outputBoundary
        );
    }

    public function test_ワンタイムトークンとワンタイムパスワードが正しい場合に、認証情報を認証済みに更新できる()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataCreator->create(
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
        $updatedAuthenticationInformaion = $this->authenticationInformaionRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationInformaion->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedAuthConfirmation = $this->authConfirmationRepository->findByToken($oneTimeTokenValue);
        $this->assertNull($deletedAuthConfirmation);
    }

    public function test_ワンタイムトークンが有効ではない場合、認証情報を認証済みに更新できない()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataCreator->create(
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
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $this->authenticationInformaionTestDataCreator->create(
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