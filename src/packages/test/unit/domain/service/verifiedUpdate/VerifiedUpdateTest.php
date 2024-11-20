<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\service\verifiedUpdate\VerifiedUpdate;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataCreator;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataFactory;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class VerifiedUpdateTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private TestUnitOfWork $unitOfWork;
    private AuthenticationInformaionTestDataCreator $authenticationInformaionTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->unitOfWork = new TestUnitOfWork();
        $this->authenticationInformaionTestDataCreator = new AuthenticationInformaionTestDataCreator($this->authenticationInformaionRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformaionRepository);
    }

    public function test_入力されたワンタイムパスワードが等しい場合、認証情報を認証済みに更新する()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $authenticationInformaion = $this->authenticationInformaionTestDataCreator->create(
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
            $this->authenticationInformaionRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork
        );

        // when
        $result = $verifiedUpdate->handle($authConfirmation, $oneTimePassword);

        // then
        // 更新が成功していることを確認
        $this->assertTrue($result);

        // 認証情報が認証済みになっていることを確認
        $updatedAuthenticationInformaion = $this->authenticationInformaionRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationInformaion->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedAuthConfirmation = $this->authConfirmationRepository->findByToken(OneTimeTokenValue::reconstruct($authConfirmation->oneTimeToken()->value()));
        $this->assertNull($deletedAuthConfirmation);
    }

    public function test_ワンタイムパスワードが正しくない場合、認証情報を認証済みに更新しない()
    {
        // given
        // 認証済みではない認証情報を保存しておく
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $authenticationInformaion = $this->authenticationInformaionTestDataCreator->create(
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
            $this->authenticationInformaionRepository,
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
        $updatedAuthenticationInformaion = $this->authenticationInformaionRepository->findById($userId);
        $this->assertEquals(VerificationStatus::Unverified, $updatedAuthenticationInformaion->verificationStatus());

        // 認証確認情報が削除されていないことを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findByToken(OneTimeTokenValue::reconstruct($authConfirmation->oneTimeToken()->value()));
        $this->assertEquals($authConfirmation->userId, $actualAuthConfirmation->userId);
        $this->assertEquals($authConfirmation->oneTimeToken()->value(), $actualAuthConfirmation->oneTimeToken()->value());
        $this->assertEquals($authConfirmation->oneTimeToken()->expirationDate(), $actualAuthConfirmation->oneTimeToken()->expirationDate());
        $this->assertEquals($authConfirmation->oneTimePassword(), $actualAuthConfirmation->oneTimePassword());
    }
}