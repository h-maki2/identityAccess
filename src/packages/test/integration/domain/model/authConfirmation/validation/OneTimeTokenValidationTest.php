<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\validation\OneTimeTokenValidation;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class OneTimeTokenValidationTest extends TestCase
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private EloquentDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator(
            $authenticationAccountRepository
        );
        $this->definitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator(
            $this->definitiveRegistrationConfirmationRepository,
            $authenticationAccountRepository
        );
    }

    public function test_ワンタイムトークンが既に存在する場合はエラーメッセージを取得できる()
    {
        // given
        // あらかじめ本登録確認情報を保存しておく
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authInfo->id());
        $既に存在するワンタイムトークン = $definitiveRegistrationConfirmation->oneTimeToken();

        $oneTimeTokenValidation = new OneTimeTokenValidation($this->definitiveRegistrationConfirmationRepository, $既に存在するワンタイムトークン);

        // when
        $result = $oneTimeTokenValidation->validate();

        // then
        // 既にワンタイムトークンが存在するのでfalseが返る
        $this->assertFalse($result);

        $expectedErrorMessageList = ['一時的なエラーが発生しました。もう一度お試しください。'];
        $this->assertEquals($expectedErrorMessageList, $oneTimeTokenValidation->errorMessageList());
    }

    public function test_ワンタイムトークンが存在しない場合はエラーメッセージを取得できない()
    {
        // given
        $oneTimeToken = OneTimeToken::create();
        $oneTimeTokenValidation = new OneTimeTokenValidation($this->definitiveRegistrationConfirmationRepository, $oneTimeToken);

        // when
        $result = $oneTimeTokenValidation->validate();

        // then
        // まだ存在しないワンタイムトークンなのでtrueが返る
        $this->assertTrue($result);

        $this->assertEmpty($oneTimeTokenValidation->errorMessageList());
    }
}