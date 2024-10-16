<?php

use packages\domain\model\userProfile\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function test_UserIdが空の場合は例外が発生することを確認()
    {
        // given
        $userId = '';

        // when・thenn
        $this->expectException(InvalidArgumentException::class);
        new UserId($userId);
    }
}