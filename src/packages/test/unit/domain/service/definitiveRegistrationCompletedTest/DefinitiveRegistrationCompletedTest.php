<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationConfirmationStatus;
use packages\domain\service\definitiveRegistrationCompleted\definitiveRegistrationCompleted;
use packages\test\helpers\definitiveRegistrationConfirmation\definitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\authenticationAccountTestDataFactory;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationConfirmedUpdateTest extends TestCase
{
    private InMemoryDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private TestTransactionManage $transactionManage;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;

    public function setUp(): void
    {
        $this->definitiveRegistrationConfirmationRepository = new InMemoryDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
    }

    public function test_入力されたワンタイムパスワードが等しい場合、認証アカウントを本登録済みに更新する()
    {
        // given
        // 本登録済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            definitiveRegistrationConfirmationStatus: definitiveRegistrationConfirmationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId
        );

        $definitiveRegistrationConfirmedUpdate = new DefinitiveRegistrationConfirmedUpdate(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );

        // when
        $definitiveRegistrationConfirmedUpdate->handle(
            $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue(), 
            $definitiveRegistrationConfirmation->oneTimePassword()
        );

        // then
        // 認証アカウントが本登録済みになっていることを確認
        $updatedAuthenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $this->assertEquals(definitiveRegistrationConfirmationStatus::Verified, $updatedAuthenticationAccount->DefinitiveRegistrationConfirmationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedDefinitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findByTokenValue($definitiveRegistrationConfirmation->oneTimeToken()->tokenValue());
        $this->assertNull($deletedDefinitiveRegistrationConfirmation);
    }

    public function test_正しくないワンタイムパスワードが入力された場合に例外が発生する()
    {
        // given
        // 本登録済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            definitiveRegistrationConfirmationStatus: definitiveRegistrationConfirmationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword
        );

        $definitiveRegistrationConfirmedUpdate = new DefinitiveRegistrationConfirmedUpdate(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを本登録済みに更新できませんでした。');
        $invalidOneTimePassword = OneTimePassword::reconstruct('654321');
        $definitiveRegistrationConfirmedUpdate->handle(
            $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue(), 
            $invalidOneTimePassword
        );
    }

    public function test_ワンタイムトークンの有効期限が切れている場合に例外が発生する()
    {
        // given
        // 本登録済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            definitiveRegistrationConfirmationStatus: definitiveRegistrationConfirmationStatus::Unverified
        );

        // 有効期限が切れているワンタイムトークンを生成
        $oneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'));
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimeTokenExpiration: $oneTimeTokenExpiration
        );

        $definitiveRegistrationConfirmedUpdate = new DefinitiveRegistrationConfirmedUpdate(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを本登録済みに更新できませんでした。');
        $definitiveRegistrationConfirmedUpdate->handle(
            $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue(), 
            $definitiveRegistrationConfirmation->oneTimePassword()
        );
    }
}