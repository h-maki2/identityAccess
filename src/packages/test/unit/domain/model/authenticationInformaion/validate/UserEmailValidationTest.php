<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\AuthenticationInformation\UserEmail;
use packages\domain\model\AuthenticationInformation\validation\UserEmailValidation;
use packages\test\helpers\AuthenticationInformation\AuthenticationInformationTestDataFactory;
use PHPUnit\Framework\TestCase;

class UserEmailValidationTest extends TestCase
{
    private AuthenticationInformationTestDataFactory $authenticationInformationTestDataFactory;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;

    public function setUp(): void
    {
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->authenticationInformationTestDataFactory = new AuthenticationInformationTestDataFactory($this->authenticationInformationRepository);
    }

    public function test_メールアドレスの形式が不正な場合はバリデーションエラーが発生する()
    {
        // given
        $email = 'test';
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationInformationRepository);

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
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationInformationRepository);

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
        $this->authenticationInformationTestDataFactory->create($userEmail);

        $userEmailValidation = new UserEmailValidation($emailString, $this->authenticationInformationRepository);

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
        $userEmailValidation = new UserEmailValidation($email, $this->authenticationInformationRepository);

        // when
        $result = $userEmailValidation->validate();

        // then
        $this->assertTrue($result);
        $this->assertEmpty($userEmailValidation->errorMessageList());
    }
}