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

        // when
        $response = $this->post('/userRegistration', [
            'email' => $userEmail,
            'password' => $userPassword
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'validationErrorMessageList' => []
        ]);
    }
}