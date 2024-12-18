<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\domain\model\authenticationAccount\validation\UserEmailValidation;
use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use packages\domain\model\common\validator\ValidationHandler;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use PHPUnit\Framework\TestCase;

class ValidationHandlerTest extends TestCase
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataFactory;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;

    public function setUp(): void
    {
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->authenticationAccountTestDataFactory = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
    }

    public function test_バリデーションエラーが発生した場合、エラーメッセージを取得できる()
    {
        // given
        // メールアドレスとパスワードのバリデーションを行う
        // 不正なメールアドレスとパスワードを設定
        $userEmailValidation = new UserEmailValidation('test', $this->authenticationAccountRepository);
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

        $expectedErrorMessageDataList = [
            new ValidationErrorMessageData('email', ['不正なメールアドレスです。']),
            new ValidationErrorMessageData('password', [
                'パスワードは8文字以上で入力してください',
                'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
            ])
        ];
        $this->assertEquals($expectedErrorMessageDataList, $validationHandler->errorMessages());
    }

    public function test_バリデーションエラーが発生しない場合、エラーメッセージは空である()
    {
        // given
        // メールアドレスとパスワードのバリデーションを行う
        $userEmailValidation = new UserEmailValidation('test@example.com', $this->authenticationAccountRepository);
        $userPasswordValidation = new UserPasswordValidation('passWord1!');

        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator($userEmailValidation);
        $validationHandler->addValidator($userPasswordValidation);

        // when
        $result = $validationHandler->validate();

        // then
        $this->assertTrue($result);
        $this->assertEmpty($validationHandler->errorMessages());
    }
}