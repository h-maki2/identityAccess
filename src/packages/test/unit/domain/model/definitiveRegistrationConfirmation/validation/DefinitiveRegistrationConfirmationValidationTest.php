<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\definitiveRegistrationConfirmation\validation\DefinitiveRegistrationConfirmationValidation;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\definitiveRegistrationConfirmation\TestDefinitiveRegistrationConfirmationFactory;
use packages\test\helpers\definitiveRegistrationConfirmation\TestOneTimeTokenFactory;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationConfirmationValidationTest extends TestCase
{
    private DefinitiveRegistrationConfirmationValidation $definitiveRegistrationConfirmationValidation;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;

    public function setUp(): void
    {
        $definitiveRegistrationConfirmationRepository = new InMemoryDefinitiveRegistrationConfirmationRepository();
        $authenticationAccountRespository = new InMemoryAuthenticationAccountRepository();
        $this->definitiveRegistrationConfirmationValidation = new DefinitiveRegistrationConfirmationValidation(
            $definitiveRegistrationConfirmationRepository
        );
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator(
            $authenticationAccountRespository
        );
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator(
            $definitiveRegistrationConfirmationRepository,
            $authenticationAccountRespository
        );
    }

    public function test_6文字ではないワンタイムパスワードが入力された場合はfalseを返す()
    {
        // given
        // あらかじめ認証確認情報を生成しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authInfo->id());

        $無効なワンタイムパスワード = '12345';
        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;

        // when
        $result = $this->definitiveRegistrationConfirmationValidation->validate($無効なワンタイムパスワード, $oneTimeTokenString);

        // then
        // 無効なワンタイムパスワードが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_26文字ではないワンタイムトークンが入力された場合はfalseを返す()
    {
        // given
        // あらかじめ認証確認情報を生成しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authInfo->id());

        $無効なワンタイムトークン = 'abcdefghijklmnopqrstuvwxyz1234';
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $無効なワンタイムトークン);

        // then
        // 無効なワンタイムトークンが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_存在しないワンタイムトークンが入力された場合はfalseを返す()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct('abcdefghijklmnopqrstuvwxyz'); 
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimeTokenValue: $oneTimeTokenValue
        );

        // when
        $存在しないワンタイムトークン = 'aaaaaaaaaaaaaaaaaaaaaaaaaa';
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;
        $result = $this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $存在しないワンタイムトークン);

        // then
        // 存在しないワンタイムトークンが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_異なるワンタイムパスワードが入力された場合にfalseを返す()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $oneTimePassword = OneTimePassword::reconstruct('111111');
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimePassword: $oneTimePassword
        );

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePasswordString = '000000';

        // when
        $result = $this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

        // then
        // 異なるワンタイムパスワードが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_ワンタイムトークンの有効期限が切れている場合にfalseを返す()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();

        // ワンタイムトークンの有効期限が2日前の認証確認情報を生成する
        $oneTimeExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-2 day'));
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimeTokenExpiration: $oneTimeExpiration
        );

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

        // then
        // ワンタイムトークンの有効期限が切れているのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_正しいワンタイムパスワードとワンタイムトークンの場合にtrueが返る()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->definitiveRegistrationConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

        // then
        // 正しいワンタイムパスワードとワンタイムトークンが入力されたのでtrueが返る
        $this->assertTrue($result);
    }
}