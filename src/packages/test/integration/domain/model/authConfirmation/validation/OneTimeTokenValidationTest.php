<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\validation\OneTimeTokenValidation;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class OneTimeTokenValidationTest extends TestCase
{
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private EloquentAuthConfirmationRepository $authConfirmationRepository;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator(
            $authenticationInformationRepository
        );
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator(
            $this->authConfirmationRepository,
            $authenticationInformationRepository
        );
    }

    public function test_ワンタイムトークンが既に存在する場合はエラーメッセージを取得できる()
    {
        // given
        // あらかじめ認証確認情報を保存しておく
        $authInfo = $this->authenticationInformationTestDataCreator->create();
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());
        $既に存在するワンタイムトークン = $authConfirmation->oneTimeToken();

        $oneTimeTokenValidation = new OneTimeTokenValidation($this->authConfirmationRepository, $既に存在するワンタイムトークン);

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
        $oneTimeTokenValidation = new OneTimeTokenValidation($this->authConfirmationRepository, $oneTimeToken);

        // when
        $result = $oneTimeTokenValidation->validate();

        // then
        // まだ存在しないワンタイムトークンなのでtrueが返る
        $this->assertTrue($result);

        $this->assertEmpty($oneTimeTokenValidation->errorMessageList());
    }
}