<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\validation\UserEmailValidation;
use packages\domain\model\authenticationInformaion\validation\UserPasswordValidation;
use packages\domain\model\common\validator\ValidationHandler;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataFactory;
use PHPUnit\Framework\TestCase;

class ValidationHandlerTest extends TestCase
{
    private AuthenticationInformaionTestDataFactory $authenticationInformaionTestDataFactory;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function setUp(): void
    {
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->authenticationInformaionRepository);
    }

    public function test_バリデーションエラーが発生した場合、エラーメッセージを取得できる()
    {
        // given
        // メールアドレスとパスワードのバリデーションを行う
        // 不正なメールアドレスとパスワードを設定
        $userEmailValidation = new UserEmailValidation('test', $this->authenticationInformaionRepository);
        $userPasswordValidation = new UserPasswordValidation('pass');

        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator($userEmailValidation);
        $validationHandler->addValidator($userPasswordValidation);

        // when
        $result = $validationHandler->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessageList = [
            'email' => ['不正なメールアドレスです。'],
            'password' => [
                'パスワードは8文字以上で入力してください',
                'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
            ]
        ];
        $this->assertEquals($expectedErrorMessageList, $validationHandler->errorMessages());
    }
}