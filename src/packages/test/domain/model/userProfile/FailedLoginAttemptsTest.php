<?php

use packages\domain\model\userProfile\FailedLoginAttempts;
use PHPUnit\Framework\TestCase;

class FailedLoginAttemptsTest extends TestCase
{
    public function test_0未満の値が入力された場合に例外が発生する()
    {
        // given
        $failedLoginAttemptsValue = -1;

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('無効な値です。');
        FailedLoginAttempts::reconstruct($failedLoginAttemptsValue);
    }

    public function test_10より大きい値が入力された場合に例外が発生する()
    {
        // given
        $failedLoginAttemptsValue = 11;

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('無効な値です。');
        FailedLoginAttempts::reconstruct($failedLoginAttemptsValue);
    }

    public function test_ログインに失敗した回数をカウントアップする()
    {
        // given
        $failedLoginAttemptsBeforeChange = FailedLoginAttempts::reconstruct(1);

        // when
        $failedLoginAttemptsAfterChange = $failedLoginAttemptsBeforeChange->add();

        // then
        // ログインに失敗した回数がカウントアップされていることを確認
        $this->assertEquals(2, $failedLoginAttemptsAfterChange->value);

        // 元のログインに失敗した回数は変更されていないことを確認
        $this->assertEquals(1, $failedLoginAttemptsBeforeChange->value);
    }

    public function test_ログイン失敗回数のカウントアップ時に、失敗回数の最大値を超えると例外が発生する()
    {
        // given
        $maxFailedLoginAttemptsValue = 10;
        $failedLoginAttempts = FailedLoginAttempts::reconstruct($maxFailedLoginAttemptsValue);

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ログイン失敗回数が最大値を超えました。');
        $failedLoginAttempts->add();
    }
}