<?php

use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationCompletedStatusTest extends TestCase
{
    public function test_ステータスが本登録済みの場合は、isCompletedメソッドの戻り値がtrueを返す()
    {
        // given
        $definitiveRegistrationCompletedStatus = DefinitiveRegistrationCompletedStatus::Completed;

        // when
        $result = $definitiveRegistrationCompletedStatus->isCompleted();

        // then
        $this->assertTrue($result);
    }

    public function test_ステータスが未認証の場合に、isCompletedメソッドの戻り値がfalseを返す()
    {
        // given
        $definitiveRegistrationCompletedStatus = DefinitiveRegistrationCompletedStatus::Incomplete;

        // when
        $result = $definitiveRegistrationCompletedStatus->isCompleted();

        // then
        $this->assertFalse($result);
    }
}