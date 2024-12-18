<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\validation\UserEmailValidation;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use PHPUnit\Framework\TestCase;

class UserEmailValidationTest extends TestCase
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;

    public function setUp(): void
    {
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
    }

    public function test_メールアドレスの形式が不正な場合はバリデーションエラーが発生する()
    {
        // given
        $email = 'test';
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationAccountRepository);

        // when
        $result = $userEmailValidation->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessage = ['不正なメールアドレスです。'];
        $this->assertEquals($expectedErrorMessage, $userEmailValidation->errorMessageList());
    }

    public function test_メールアドレスが256文字以上の場合はエラーメッセージが発生する()
    {
        // given
        $email = str_repeat('a', 244) . '@example.com';
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationAccountRepository);

        // when
        $result = $userEmailValidation->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessage = ['不正なメールアドレスです。'];
        $this->assertEquals($expectedErrorMessage, $userEmailValidation->errorMessageList());
    }

    public function test_メールアドレスが既に登録されている場合はバリデーションエラーが発生する()
    {
        // given
        // test@example.comのメールアドレスが既に登録されている
        $emailString = 'test@example.com';
        $userEmail = new UserEmail($emailString);
        $this->authenticationAccountTestDataCreator->create($userEmail);

        $userEmailValidation = new UserEmailValidation($emailString, $this->authenticationAccountRepository);

        // when
        $result = $userEmailValidation->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessage = ['既に登録されているメールアドレスです。'];
        $this->assertEquals($expectedErrorMessage, $userEmailValidation->errorMessageList());
    }

    public function test_適切な形式のメールアドレスで尚且つ未登録のアドレスの場合はバリデーションエラーが発生しない()
    {
        // given
        $email = 'test@example.com';
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationAccountRepository);

        // when
        $result = $userEmailValidation->validate();

        // then
        $this->assertTrue($result);
        $this->assertEmpty($userEmailValidation->errorMessageList());
    }
}