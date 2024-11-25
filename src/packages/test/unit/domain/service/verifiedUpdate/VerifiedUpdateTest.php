<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataFactory;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class VerifiedUpdateTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestUnitOfWork $unitOfWork;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->unitOfWork = new TestUnitOfWork();
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
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword
        );

        $verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork
        );

        // when
        $result = $verifiedUpdate->handle($authConfirmation, $oneTimePassword);

        // then
        // 更新が成功していることを確認
        $this->assertTrue($result);

        // 認証情報が認証済みになっていることを確認
        $updatedAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationInformation->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedAuthConfirmation = $this->authConfirmationRepository->findByToken(OneTimeTokenValue::reconstruct($authConfirmation->oneTimeToken()->value()));
        $this->assertNull($deletedAuthConfirmation);
    }

    public function test_ワンタイムパスワードが正しくない場合、認証情報を認証済みに更新しない()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformationRepository->nextUserId();
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
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
            $this->unitOfWork
        );

        // when
        $invalidOneTimePassword = OneTimePassword::reconstruct('654321');
        $result = $verifiedUpdate->handle($authConfirmation, $invalidOneTimePassword);

        // then
        // 更新が失敗していることを確認
        $this->assertFalse($result);

        // 認証情報が認証済みになっていないことを確認
        $updatedAuthenticationInformation = $this->authenticationInformationRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Unverified, $updatedAuthenticationInformation->verificationStatus());

        // 認証確認情報が削除されていないことを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findByToken(OneTimeTokenValue::reconstruct($authConfirmation->oneTimeToken()->value()));
        $this->assertEquals($authConfirmation->userId, $actualAuthConfirmation->userId);
        $this->assertEquals($authConfirmation->oneTimeToken()->value(), $actualAuthConfirmation->oneTimeToken()->value());
        $this->assertEquals($authConfirmation->oneTimeToken()->expirationDate(), $actualAuthConfirmation->oneTimeToken()->expirationDate());
        $this->assertEquals($authConfirmation->oneTimePassword(), $actualAuthConfirmation->oneTimePassword());
    }
}