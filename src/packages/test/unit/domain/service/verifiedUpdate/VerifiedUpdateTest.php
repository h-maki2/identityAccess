<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataFactory;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class VerifiedUpdateTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestTransactionManage $transactionManage;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);
    }

    public function test_入力されたワンタイムパスワードが等しい場合、認証情報を認証済みに更新する()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $userId
        );

        $verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->transactionManage
        );

        // when
        $verifiedUpdate->handle(
            $authConfirmation->oneTimeToken()->tokenValue(), 
            $authConfirmation->oneTimePassword()
        );

        // then
        // 認証情報が認証済みになっていることを確認
        $updatedAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationInformation->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedAuthConfirmation = $this->authConfirmationRepository->findByTokenValue($authConfirmation->oneTimeToken()->tokenValue());
        $this->assertNull($deletedAuthConfirmation);
    }

    public function test_正しくないワンタイムパスワードが入力された場合に例外が発生する()
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
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword
        );

        $verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->transactionManage
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証情報を認証済みに更新できませんでした。');
        $invalidOneTimePassword = OneTimePassword::reconstruct('654321');
        $verifiedUpdate->handle(
            $authConfirmation->oneTimeToken()->tokenValue(), 
            $invalidOneTimePassword
        );
    }

    public function test_ワンタイムトークンの有効期限が切れている場合に例外が発生する()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 有効期限が切れているワンタイムトークンを生成
        $oneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'));
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimeTokenExpiration: $oneTimeTokenExpiration
        );

        $verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->transactionManage
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証情報を認証済みに更新できませんでした。');
        $verifiedUpdate->handle(
            $authConfirmation->oneTimeToken()->tokenValue(), 
            $authConfirmation->oneTimePassword()
        );
    }
}