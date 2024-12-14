<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\service\authConfirmation\OneTimeTokenExistsService;
use packages\test\helpers\authConfirmation\TestAuthConfirmationFactory;
use packages\test\helpers\authConfirmation\TestOneTimeTokenFactory;
use packages\test\helpers\authenticationInformation\TestUserIdFactory;
use PHPUnit\Framework\TestCase;

class AuthConfirmationTest extends TestCase
{
    private OneTimeTokenExistsService $oneTimeTokenExistsService;

    public function setUp(): void
    {
        $this->OneTimeTokenExistsService = new OneTimeTokenExistsService(new InMemoryAuthConfirmationRepository());
    }

    public function test_認証情報を作成する()
    {
        // given
        $userId = TestUserIdFactory::createUserId();
        $oneToken = OneTimeToken::create();

        // when
        $authConfirmation = AuthConfirmation::create($userId, $oneToken, $this->OneTimeTokenExistsService);

        // then
        // 入力したユーザーIDが取得できることを確認
        $this->assertEquals($userId, $authConfirmation->userId);

        // ワンタイムトークンが生成されていることを確認
        $expectedOneTimeTokenExpiration = new DateTimeImmutable('+24 hours');
        $this->assertEquals($expectedOneTimeTokenExpiration->format('Y-m-d H:i'), $authConfirmation->oneTimeToken()->expirationDate());
        $this->assertEquals(26, strlen($authConfirmation->oneTimeToken()->tokenValue()->value));

        // ワンタイムパスワードが生成されていることを確認
        $this->assertEquals(6, strlen((string)$authConfirmation->oneTimePassword()->value));
    }

    public function test_認証確認情報が有効期限切れであることを判定できる()
    {
        // given
        // 有効期限が1分前のワンタイムトークンを作成
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            null,
            OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation(
            null,
            $oneTimeToken
        );

        // when
        $result = $authConfirmation->isExpired(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_認証確認情報が有効期限内であることを判定できる()
    {
        // given
        // 有効期限が1分後のワンタイムトークンを作成
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            null,
            OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('+1 minutes'))
        );
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation(
            null,
            $oneTimeToken
        );

        // when
        $result = $authConfirmation->isExpired(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_認証確認情報の再取得を行う()
    {
        // given
        $userId = TestUserIdFactory::createUserId();
        $oneTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            null,
            OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $authConfirmation = AuthConfirmation::reconstruct(
            $userId,
            $oneTimeToken,
            $oneTimePassword
        );

        // when
        $authConfirmation->reObtain();

        // then
        // ワンタイムトークンが再生成されていることを確認する
        $this->assertNotEquals($oneTimeToken, $authConfirmation->oneTimeToken());
        $expectedOneTimeTokenExpiration = new DateTimeImmutable('+24 hours');
        // ワンタイムトークンの有効期限が24時間後であることを確認
        $this->assertEquals($expectedOneTimeTokenExpiration->format('Y-m-d H:i'), $authConfirmation->oneTimeToken()->expirationDate());

        // ワンタイムパスワードが再生成されていることを確認する
        $this->assertNotEquals($oneTimePassword, $authConfirmation->oneTimePassword());

        // ユーザーIDは変更されていないことを確認する
        $this->assertEquals($userId, $authConfirmation->userId);
    }
}