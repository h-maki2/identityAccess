<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\domain\service\registration\definitiveRegistration\UserDefinitiveRegistrationUpdate;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\authenticationAccountTestDataFactory;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class UserDefinitiveRegistrationUpdateTest extends TestCase
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
            definitiveRegistrationCompletedStatus: DefinitiveRegistrationCompletedStatus::Incomplete
        );
        // 本登録確認情報を保存しておく
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId
        );

        $UserDefinitiveRegistrationUpdate = new UserDefinitiveRegistrationUpdate(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );

        // when
        $UserDefinitiveRegistrationUpdate->handle(
            $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue(), 
            $definitiveRegistrationConfirmation->oneTimePassword()
        );

        // then
        // 認証アカウントが本登録済みになっていることを確認
        $updatedAuthenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $this->assertEquals(DefinitiveRegistrationCompletedStatus::Completed, $updatedAuthenticationAccount->definitiveRegistrationCompletedStatus());

        // 本登録確認情報が削除されていることを確認
        $deletedDefinitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findByTokenValue($definitiveRegistrationConfirmation->oneTimeToken()->tokenValue());
        $this->assertNull($deletedDefinitiveRegistrationConfirmation);
    }
}