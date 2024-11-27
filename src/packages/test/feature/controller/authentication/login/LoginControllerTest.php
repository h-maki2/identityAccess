<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_メールアドレスとパスワードが異なる場合にログインに失敗する()
    {
        // given
        $存在しないメールアドレス = 'test@example.com';
        $存在しないパスワード = 'password';

        // when
        $response = $this->post('/login', [
            'email' => $存在しないメールアドレス,
            'password' => $存在しないパスワード,
            'client_id' => '6',
            'redirect_url' => 'http://identity.todoapp.local/auth/callback',
            'response_type' => 'code'
        ]);

        // then
        $response->assertStatus(400);
        $response->assertJson([
            'authorizationUrl' => '',
            'loginSucceeded' => false,
            'accountLocked' => false
        ]);
    }
}