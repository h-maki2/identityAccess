<?php

use packages\domain\model\authConfirmation\TemporaryTokenExpiration;
use PHPUnit\Framework\TestCase;

class TemporaryTokenExpirationTest extends TestCase
{
    public function test_一時トークンの有効期限は24時間後()
    {
        // given

        // when
        $actual = TemporaryTokenExpiration::create();

        // then
        $expectedDateTime = new DateTimeImmutable('+24 hours');
        $this->assertEquals($expectedDateTime->format('Y-m-d H:i'), $actual->formattedValue());
    }

    public function test_一時トークンが有効期限内であることを判定できる()
    {
        // given
        // １分後が有効期限
        $temporaryTokenExpiration = TemporaryTokenExpiration::reconstruct(new DateTimeImmutable('+1 minutes'));

        // when
        $result = $temporaryTokenExpiration->isExpired(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_一時トークンが有効期限外であることを判定できる()
    {
        // given
        // １分前が有効期限
        $temporaryTokenExpiration = TemporaryTokenExpiration::reconstruct(new DateTimeImmutable('-1 minutes'));

        // when
        $result = $temporaryTokenExpiration->isExpired(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }
}