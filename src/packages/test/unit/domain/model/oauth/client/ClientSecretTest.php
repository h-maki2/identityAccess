<?php

use packages\domain\model\oauth\client\ClientSecret;
use PHPUnit\Framework\TestCase;

class ClientSecretTest extends TestCase
{
    public function test_euqlsメソッドの引数に入力したクライアントシークレットと同じクライアントシークレットを持っている場合にtrueを返す()
    {
        // given
        $clientSecret = new ClientSecret('client_secret');
        $other = new ClientSecret('client_secret');

        // when
        $result = $clientSecret->equals($other);

        // then
        $this->assertTrue($result);
    }

    public function test_equalsメソッドの引数に入力したクライアントシークレットと異なるクライアントシークレットを持っている場合にfalseを返す()
    {
        // given
        $clientSecret = new ClientSecret('client_secret1');
        $other = new ClientSecret('client_secret2');

        // when
        $result = $clientSecret->equals($other);

        // then
        $this->assertFalse($result);
    }
}