<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProvisionalRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_メールアドレスとパスワードを入力してユーザー登録を行う()
    {
        // given
        $userEmail = 'test@exmaple.com';
        $userPassword = 'abcABC123!';
        $userPasswordConfirmation = 'abcABC123!';

        // when
        $response = $this->post('/provisionalRegister', [
            'email' => $userEmail,
            'password' => $userPassword,
            'passwordConfirmation' => $userPasswordConfirmation
        ]);

        // then
        $response->assertStatus(200);
        // ユーザー仮登録完了画面に遷移することを確認する
        $content = htmlspecialchars_decode($response->getContent());
        $this->assertStringContainsString('<title>ユーザー仮登録完了</title>', $content);
    }

    public function test_メールアドレスの形式とパスワードの形式が不正な場合にユーザー登録に失敗する()
    {
        // given
        $userEmail = 'test'; // メールアドレスの形式が異なる
        $userPassword = 'abcABC123'; // パスワードの形式が異なる
        $userPasswordConfirmation = 'abcABC123';

        // when
        $response = $this->post('/provisionalRegister', [
            'email' => $userEmail,
            'password' => $userPassword,
            'passwordConfirmation' => $userPasswordConfirmation
        ]);

        // then
        // ユーザー登録画面にリダイレクトされることを確認
        $response->assertStatus(302);
    }
}