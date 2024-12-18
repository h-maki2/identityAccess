<?php

use packages\domain\model\authenticationAccount\LoginRestrictionStatus;
use PHPUnit\Framework\TestCase;

class LoginRestrictionStatusTest extends TestCase
{
    public function test_ログイン制限されている場合を判定できる()
    {
        // given
        $loginRestrictionStatus = LoginRestrictionStatus::Restricted;

        // when
        $result = $loginRestrictionStatus->isRestricted();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限されていない場合を判定できる()
    {
        // given
        $loginRestrictionStatus = LoginRestrictionStatus::Unrestricted;

        // when
        $result = $loginRestrictionStatus->isRestricted();

        // then
        $this->assertFalse($result);
    }
}