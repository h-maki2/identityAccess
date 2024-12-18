<?php

use packages\domain\model\authenticationAccount\VerificationStatus;
use PHPUnit\Framework\TestCase;

class VerificationStatusTest extends TestCase
{
    public function test_ステータスが認証済みの場合に正しくインスタンスを生成できる()
    {
        // given

        // when
        $verificationStatus = VerificationStatus::Verified;

        // then
        $this->assertEquals('1', $verificationStatus->value);
        $this->assertEquals('認証済み', $verificationStatus->displayValue());
    }

    public function test_ステータスが未認証の場合に正しくインスタンスを生成できる()
    {
        // given

        // when
        $verificationStatus = VerificationStatus::Unverified;

        // then
        $this->assertEquals('0', $verificationStatus->value);
        $this->assertEquals('未認証', $verificationStatus->displayValue());
    }

    public function test_ステータスが認証済みの場合は、isVerifiedメソッドの戻り値がtrueを返す()
    {
        // given
        $verificationStatus = VerificationStatus::Verified;

        // when
        $result = $verificationStatus->isVerified();

        // then
        $this->assertTrue($result);
    }

    public function test_ステータスが未認証の場合に、isVerifiedメソッドの戻り値がfalseを返す()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;

        // when
        $result = $verificationStatus->isVerified();

        // then
        $this->assertFalse($result);
    }
}