<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserRegistrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_メールアドレスとパスワードを入力してユーザー登録を行う()
    {
        // given
        $userEmail = 'test@exmaple.com';
        $userPassword = 'abcABC123!';
        $userPasswordConfirmation = 'abcABC123!';

        // when
        $response = $this->withHeaders([
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api//userRegistration', [
            'email' => $userEmail,
            'password' => $userPassword,
            'password_confirmation' => $userPasswordConfirmation
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => []
        ]);
    }

    public function test_メールアドレスの形式とパスワードの形式が不正な場合にユーザー登録に失敗する()
    {
        // given
        $userEmail = 'test'; // メールアドレスの形式が異なる
        $userPassword = 'abcABC123'; // パスワードの形式が異なる
        $userPasswordConfirmation = 'abcABC123';

        // when
        $response = $this->withHeaders([
            'Accept' => 'application/vnd.example.v1+json'
        ])->post('/api//userRegistration', [
            'email' => $userEmail,
            'password' => $userPassword,
            'password_confirmation' => $userPasswordConfirmation
        ]);

        // then
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'Bad Request',
                'details' => [
                    'email' => ['不正なメールアドレスです。'],
                    'password' => ['パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください']
                ]
            ]
        ]);
    }
}