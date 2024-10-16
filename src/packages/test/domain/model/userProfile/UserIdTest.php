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
        $this->expectExceptionMessage('ユーザーIDが空です。');
        new UserId($userIdString);
    }

    public function test_適切なULIDを入力した場合にUserIdが正しく生成される()
    {
        // given
        $userIdString = '01FVSHW3S7HW03J702MAE82MQS';

        // when
        $userId = new UserId($userIdString);

        // then
        $this->assertInstanceOf(UserId::class, $userId);
    }

    public function test_不適切なULIDを入力した場合に例外が発生する_27文字のULIDの場合()
    {
        // given 27文字のULID
        $userIdString = '01FVSHW3S7HW03J702MAE82MQSO';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザーIDは26文字です。');
        new UserId($userIdString);
    }

    public function test_不適切なULIDを入力した場合に例外が発生する_25文字のULIDの場合()
    {
        // given 25文字のULID
        $userIdString = '01FVSHW3S7HW03J702MAE82MQ';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザーIDは26文字です。');
        new UserId($userIdString);
    }
}