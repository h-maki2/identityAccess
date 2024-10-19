<?php

use packages\domain\model\userProfile\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function test_UserIdが空の場合は例外が発生する()
    {
        // given
        $userIdString = '';

        // when・thenn
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザーIDは36文字です。');
        new UserId($userIdString);
    }

    public function test_UserIdが正しく生成される()
    {
        // given
        $userIdString = '0188b2a6-bd94-7ccf-9666-1df7e26ac6b8';

        // when
        $userId = new UserId($userIdString);

        // then
        $this->assertEquals($userIdString, $userId->value);
    }

    public function test_文字数が不適切なUserIdを入力した場合に例外が発生する_37文字のULIDの場合()
    {
        // given 37文字
        $userIdString = '0188b2a6-bd94-7ccf-9666-1df7e26ac6b89';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザーIDは36文字です。');
        new UserId($userIdString);
    }

    public function test_文字数が不適切なUserIdを入力した場合に例外が発生する_35文字のULIDの場合()
    {
        // given 35文字
        $userIdString = '0188b2a6-bd94-7ccf-9666-1df7e26ac6b';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザーIDは36文字です。');
        new UserId($userIdString);
    }

    public function test_UUIDver7の形式ではないUserIdだったら例外が発生する()
    {
        // given
        $userIdString = '0188b2a6-bd94-6ccf-9666-1df7e26ac6b2';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UUID ver7の形式になっていません。');
        new UserId($userIdString);
    }
}