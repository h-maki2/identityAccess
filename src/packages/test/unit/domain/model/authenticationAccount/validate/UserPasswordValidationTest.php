<?php

use packages\domain\model\authenticationAccount\validation\UserPasswordValidation;
use PHPUnit\Framework\TestCase;

class UserPasswordValidationTest extends TestCase
{
    public function test_パスワードが8文字未満の場合はバリデーションエラーが発生する()
    {
        // given
        $password = 'pasAA!8';
        $validation = new UserPasswordValidation($password);

        // when
        $result = $validation->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessageList = ['パスワードは8文字以上で入力してください'];
        $this->assertEquals($expectedErrorMessageList, $validation->errorMessageList());
    }

    public function test_パスワードに大文字、小文字、数字、記号がそれぞれ一文字含んでいない場合はバリデーションエラーが発生する()
    {
        // given
        $password = 'password';
        $validation = new UserPasswordValidation($password);

        // when
        $result = $validation->validate();

        // then
        $this->assertFalse($result);

        $expectedErrorMessageList = ['パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'];
        $this->assertEquals($expectedErrorMessageList, $validation->errorMessageList());
    }

    public function test_パスワードが8文字未満で尚且つ、大文字、小文字、数字、記号がそれぞれ一文字含んでいない場合はバリデーションエラーが発生する()
    {
        // given
        $password = 'pass';
        $validation = new UserPasswordValidation($password);

        // when
        $result = $validation->validate();

        // then
        $this->assertFalse($result);
        $expectedErrorMessageList = [
            'パスワードは8文字以上で入力してください',
            'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
        ];
        $this->assertEquals($expectedErrorMessageList, $validation->errorMessageList());
    }

    public function test_パスワードが8文字以上で、大文字、小文字、数字、記号がそれぞれ一文字含んでいる場合はバリデーションエラーが発生しない()
    {
        // given
        $password = 'Passw0rd!';
        $validation = new UserPasswordValidation($password);

        // when
        $result = $validation->validate();

        // then
        $this->assertTrue($result);
        $this->assertEmpty($validation->errorMessageList());
    }
}