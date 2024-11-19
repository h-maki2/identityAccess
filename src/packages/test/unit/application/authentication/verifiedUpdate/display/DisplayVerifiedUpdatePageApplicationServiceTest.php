<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageApplicationService;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use PHPUnit\Framework\TestCase;

class DisplayVerifiedUpdatePageApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository);
    }

    public function test_有効なワンタイムトークンの場合に、認証済み更新ページを表示できる()
    {
        // given
        // 有効な認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimeTokenExpiration = OneTimeTokenExpiration::create(new DateTimeImmutable('+1 day'));
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $this->authConfirmationTestDataCreator->create(
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimeTokenExpiration: $oneTimeTokenExpiration,
            oneTimePassword: $oneTimePassword
        );

        // when
        $displayVerifiedUpdatePageApplicationService = new DisplayVerifiedUpdatePageApplicationService($this->authConfirmationRepository);
        $result = $displayVerifiedUpdatePageApplicationService->displayVerifiedUpdatePage($oneTimeTokenValue->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessage);

        // ワンタイムトークンとワンタイムパスワードが取得できることを確認
        $this->assertEquals($oneTimeTokenValue->value, $result->oneTimeTokenValue);
        $this->assertEquals($oneTimePassword->value, $result->oneTimePassword);
    }
}