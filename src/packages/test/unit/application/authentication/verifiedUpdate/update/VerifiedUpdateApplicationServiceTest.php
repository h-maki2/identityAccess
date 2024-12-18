<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedApplicationService;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedOutputBoundary;
use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedResult;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\domain\service\definitiveRegistrationCompleted\definitiveRegistrationCompleted;
use packages\test\helpers\definitiveRegistrationConfirmation\definitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationCompletedApplicationServiceTest extends TestCase
{
    private InMemoryDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private DefinitiveRegistrationCompleted $DefinitiveRegistrationCompleted;
    private TestTransactionManage $transactionManage;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationCompletedApplicationService $DefinitiveRegistrationCompletedApplicationService;
    private DefinitiveRegistrationCompletedResult $capturedResult;

    public function setUp(): void
    {
        $this->definitiveRegistrationConfirmationRepository = new InMemoryDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->DefinitiveRegistrationCompleted = new DefinitiveRegistrationCompleted(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);

        $this->DefinitiveRegistrationCompletedApplicationService = new DefinitiveRegistrationCompletedApplicationService(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage
        );
    }

    public function test_ワンタイムトークンとワンタイムパスワードが正しい場合に、認証アカウントを確認済みに更新できる()
    {
        // given
        // 確認済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::create();
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 正しいワンタイムトークンとワンタイムパスワードを入力する
        $result = $this->DefinitiveRegistrationCompletedApplicationService->DefinitiveRegistrationCompleted($oneTimeTokenValue->value, $oneTimePassword->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessage);

        // 認証アカウントが確認済みになっていることを確認
        $updatedAuthenticationAccount = $this->authenticationAccountRepository->findById($userId, UnsubscribeStatus::Subscribed);
        $this->assertEquals(VerificationStatus::Verified, $updatedAuthenticationAccount->verificationStatus());

        // 認証確認情報が削除されていることを確認
        $deletedDefinitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue);
        $this->assertNull($deletedDefinitiveRegistrationConfirmation);
    }

    public function test_ワンタイムトークンが有効ではない場合、認証アカウントを確認済みに更新できない()
    {
        // given
        // 確認済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct('abcdefghijklmnopqrstuvwxya');
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 存在しないワンタイムトークンを生成
        $invalidOneTimeTokenValue = OneTimeTokenValue::reconstruct('aaaaaaaaaaaaaaaaaaaaaaaaaa');
        $result = $this->DefinitiveRegistrationCompletedApplicationService->DefinitiveRegistrationCompleted($invalidOneTimeTokenValue->value, $oneTimePassword->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('ワンタイムトークンかワンタイムパスワードが無効です。', $result->validationErrorMessage);
    }

    public function test_ワンタイムパスワードが正しくない場合、認証アカウントを確認済みに更新できない()
    {
        // given
        // 確認済みではない認証アカウントを保存しておく
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );
        // 認証確認情報を保存しておく
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimePassword: $oneTimePassword,
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        // 正しくないワンタイムパスワードを入力する
        $invalidOneTimePassword = OneTimePassword::reconstruct('654321');
        $result = $this->DefinitiveRegistrationCompletedApplicationService->DefinitiveRegistrationCompleted($oneTimeTokenValue->value, $invalidOneTimePassword->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('ワンタイムトークンかワンタイムパスワードが無効です。', $result->validationErrorMessage);
    }
}