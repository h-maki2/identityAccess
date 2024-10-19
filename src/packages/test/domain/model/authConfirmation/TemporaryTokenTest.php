<?php

use packages\domain\model\authConfirmation\TemporaryToken;
use packages\domain\service\common\token\FetchElapsedTimeFromUUIDver7;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TemporaryTokenTest extends TestCase
{
    public function test_トークンの形式がUUIDver7ではない場合、インスタンスを生成できない()
    {
        // given
        $invalidTokenString = '0188b2a6-bd94-6ccf-9666-1df7e26ac6b2';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UUID ver7の形式になっていません。');
        new TemporaryToken($invalidTokenString);
    }

    public function test_トークンの文字数が36文字ではない場合、インスタンスを生成できない_37文字の場合()
    {
        // given 37文字のトークン
        $invalidTokenString = '0188b2a6-bd94-7ccf-9666-1df7e26ac6b89';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('TemporaryTokenは36文字です。');
        new TemporaryToken($invalidTokenString);
    }

    public function test_トークンの文字数が36文字ではない場合、インスタンスを生成できない_35文字の場合()
    {
        // given 35文字のトークン
        $invalidTokenString = '0188b2a6-bd94-7ccf-9666-1df7e26ac6b';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('TemporaryTokenは36文字です。');
        new TemporaryToken($invalidTokenString);
    }

    public function test_有効なトークンかどうかを判定できる()
    {
        // given
        $token = new TemporaryToken(Uuid::uuid7());

        // 現在の時刻から23時間59分59秒後のDateTimeを取得
        $dateTime = new DateTime();
        $interval = new DateInterval('PT23H59M59S');
        $dateTime->add($interval);

        // when
        $result = $token->isValid(new FetchElapsedTimeFromUUIDver7(), $dateTime);

        // then トークンが生成されてから24時間以内の場合は有効なトークン
        $this->assertTrue($result);
    }

    public function test_無効なトークンかどうかを判定できる()
    {
        // given
        $token = new TemporaryToken(Uuid::uuid7());

        // 現在の時刻から24時間00分01秒後のDateTimeを取得
        $dateTime = new DateTime();
        $interval = new DateInterval('PT24H00M01S');
        $dateTime->add($interval);

        // when
        $result = $token->isValid(new FetchElapsedTimeFromUUIDver7(), $dateTime);

        // then トークンが生成されてから24時間を超えている場合は無効なトークン
        $this->assertFalse($result);
    }
}