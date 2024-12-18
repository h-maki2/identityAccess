<?php

use packages\domain\model\authenticationAccount\DefinitiveRegistrationConfirmationStatus;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationConfirmationStatusTest extends TestCase
{
    public function test_ステータスが本登録済みの場合は、isVerifiedメソッドの戻り値がtrueを返す()
    {
        // given
        $definitiveRegistrationConfirmationStatus = definitiveRegistrationConfirmationStatus::Verified;

        // when
        $result = $definitiveRegistrationConfirmationStatus->isVerified();

        // then
        $this->assertTrue($result);
    }

    public function test_ステータスが未認証の場合に、isVerifiedメソッドの戻り値がfalseを返す()
    {
        // given
        $definitiveRegistrationConfirmationStatus = definitiveRegistrationConfirmationStatus::Unverified;

        // when
        $result = $definitiveRegistrationConfirmationStatus->isVerified();

        // then
        $this->assertFalse($result);
    }
}