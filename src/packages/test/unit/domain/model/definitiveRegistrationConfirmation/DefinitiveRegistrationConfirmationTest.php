<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\service\registration\definitiveRegistration\OneTimeTokenExistsService;
use packages\test\helpers\definitiveRegistrationConfirmation\TestDefinitiveRegistrationConfirmationFactory;
use packages\test\helpers\definitiveRegistrationConfirmation\TestOneTimeTokenFactory;
use packages\test\helpers\authenticationAccount\TestUserIdFactory;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationConfirmationTest extends TestCase
{
    private OneTimeTokenExistsService $oneTimeTokenExistsService;

    public function setUp(): void
    {
        $this->oneTimeTokenExistsService = new OneTimeTokenExistsService(new InMemoryDefinitiveRegistrationConfirmationRepository());
    }

    public function test_認証アカウントを作成する()
    {
        // given
        $userId = TestUserIdFactory::createUserId();
        $oneToken = OneTimeToken::create();

        // when
        $definitiveRegistrationConfirmation = DefinitiveRegistrationConfirmation::create($userId, $oneToken, $this->oneTimeTokenExistsService);

        // then
        // 入力したユーザーIDが取得できることを確認
        $this->assertEquals($userId, $definitiveRegistrationConfirmation->userId);

        // ワンタイムトークンが生成されていることを確認
        $expectedOneTimeTokenExpiration = new DateTimeImmutable('+24 hours');
        $this->assertEquals($expectedOneTimeTokenExpiration->format('Y-m-d H:i'), $definitiveRegistrationConfirmation->oneTimeToken()->expirationDate());
        $this->assertEquals(26, strlen($definitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value));

        // ワンタイムパスワードが生成されていることを確認
        $this->assertEquals(6, strlen((string)$definitiveRegistrationConfirmation->oneTimePassword()->value));
    }

    public function test_本登録確認情報の再取得を行う()
    {
        // given
        $userId = TestUserIdFactory::createUserId();
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            expiration: OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $definitiveRegistrationConfirmation = DefinitiveRegistrationConfirmation::reconstruct(
            $userId,
            $oneTimeToken,
            $oneTimePassword
        );

        // when
        $definitiveRegistrationConfirmation->reObtain();

        // then
        $expectedOneTimeTokenExpiration = new DateTimeImmutable('+24 hours');
        // ワンタイムトークンの有効期限が24時間後であることを確認
        $this->assertEquals($expectedOneTimeTokenExpiration->format('Y-m-d H:i'), $definitiveRegistrationConfirmation->oneTimeToken()->expirationDate());

        // ユーザーIDは変更されていないことを確認する
        $this->assertEquals($userId, $definitiveRegistrationConfirmation->userId);
    }
}