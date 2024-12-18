<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authConfirmation\TestAuthConfirmationFactory;
use packages\test\helpers\authConfirmation\TestOneTimeTokenFactory;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use PHPUnit\Framework\TestCase;

class AuthConfirmationValidationTest extends TestCase
{
    private AuthConfirmationValidation $authConfirmationValidation;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    public function setUp(): void
    {
        $authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $authenticationAccountRespository = new InMemoryAuthenticationAccountRepository();
        $this->authConfirmationValidation = new AuthConfirmationValidation(
            $authConfirmationRepository
        );
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator(
            $authenticationAccountRespository
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator(
            $authConfirmationRepository,
            $authenticationAccountRespository
        );
    }

    public function test_6文字ではないのワンタイムパスワードが入力された場合はfalseを返す()
    {
        // given
        // あらかじめ認証確認情報を生成しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $無効なワンタイムパスワード = '12345';
        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;

        // when
        $result = $this->authConfirmationValidation->validate($無効なワンタイムパスワード, $oneTimeTokenString);

        // then
        // 無効なワンタイムパスワードが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_26文字ではないワンタイムトークンが入力された場合はfalseを返す()
    {
        // given
        // あらかじめ認証確認情報を生成しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $無効なワンタイムトークン = 'abcdefghijklmnopqrstuvwxyz1234';
        $oneTimePassword = $authConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->authConfirmationValidation->validate($oneTimePasswordString, $無効なワンタイムトークン);

        // then
        // 無効なワンタイムトークンが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_存在しないワンタイムトークンが入力された場合はfalseを返す()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        // when
        $存在しないワンタイムトークン = 'aaaaaaaaaaaaaaaaaaaaaaaaaa';
        $oneTimePassword = $authConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;
        $result = $this->authConfirmationValidation->validate($oneTimePasswordString, $存在しないワンタイムトークン);

        // then
        // 存在しないワンタイムトークンが入力されたのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_異なるワンタイムパスワードが入力された場合にfalseを返す()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePasswordString = '000000';

        // when
        $result = $this->authConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

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
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimeTokenExpiration: $oneTimeExpiration
        );

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePassword = $authConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->authConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

        // then
        // ワンタイムトークンの有効期限が切れているのでfalseが返る
        $this->assertFalse($result);
    }

    public function test_正しいワンタイムパスワードとワンタイムトークンの場合にtrueが返る()
    {
        // given
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenString = $oneTimeToken->tokenValue()->value;
        $oneTimePassword = $authConfirmation->oneTimePassword();
        $oneTimePasswordString = $oneTimePassword->value;

        // when
        $result = $this->authConfirmationValidation->validate($oneTimePasswordString, $oneTimeTokenString);

        // then
        // 正しいワンタイムパスワードとワンタイムトークンが入力されたのでtrueが返る
        $this->assertTrue($result);
    }
}