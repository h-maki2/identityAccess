<?php

use packages\domain\model\authenticationAccount\DefinitiveRegistrationConfirmationStatus;
use PHPUnit\Framework\TestCase;

class DefinitiveRegistrationConfirmationStatusTest extends TestCase
{
    public function test_ステータスが本登録済みの場合に正しくインスタンスを生成できる()
    {
        // given

        // when
        $definitiveRegistrationConfirmationStatus = definitiveRegistrationConfirmationStatus::Verified;

        // then
        $this->assertEquals('1', $definitiveRegistrationConfirmationStatus->value);
        $this->assertEquals('本登録済み', $definitiveRegistrationConfirmationStatus->displayValue());
    }

    public function test_ステータスが未認証の場合に正しくインスタンスを生成できる()
    {
        // given

        // when
        $definitiveRegistrationConfirmationStatus = definitiveRegistrationConfirmationStatus::Unverified;

        // then
        $this->assertEquals('0', $definitiveRegistrationConfirmationStatus->value);
        $this->assertEquals('未認証', $definitiveRegistrationConfirmationStatus->displayValue());
    }

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