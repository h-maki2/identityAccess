<?php

use packages\domain\model\authenticationAccount\FailedLoginCount;
use PHPUnit\Framework\TestCase;

class FailedLoginCountTest extends TestCase
{
    public function test_ログイン失敗回数を初期化する()
    {
        // given

        // when
        $FailedLoginCount = FailedLoginCount::initialization();

        // then
        $this->assertEquals(0, $FailedLoginCount->value);
    }

    public function test_0未満の値が入力された場合に例外が発生する()
    {
        // given
        $FailedLoginCountValue = -1;

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('無効な値です。');
        FailedLoginCount::reconstruct($FailedLoginCountValue);
    }

    public function test_10より大きい値が入力された場合に例外が発生する()
    {
        // given
        $FailedLoginCountValue = 11;

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('無効な値です。');
        FailedLoginCount::reconstruct($FailedLoginCountValue);
    }

    public function test_ログインに失敗した回数をカウントアップする()
    {
        // given
        $FailedLoginCountBeforeChange = FailedLoginCount::reconstruct(1);

        // when
        $FailedLoginCountAfterChange = $FailedLoginCountBeforeChange->add();

        // then
        // ログインに失敗した回数がカウントアップされていることを確認
        $this->assertEquals(2, $FailedLoginCountAfterChange->value);

        // 元のログインに失敗した回数は変更されていないことを確認
        $this->assertEquals(1, $FailedLoginCountBeforeChange->value);
    }

    public function test_ログイン失敗回数のカウントアップ時に、失敗回数の最大値を超えると例外が発生する()
    {
        // given
        $maxFailedLoginCountValue = 10;
        $FailedLoginCount = FailedLoginCount::reconstruct($maxFailedLoginCountValue);

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $FailedLoginCount->add();
    }

    public function test_ログイン失敗回数が、アカウントロックのしきい値に達した場合を判定できる()
    {
        // given
        $maxFailedLoginCountValue = 10;
        $FailedLoginCount = FailedLoginCount::reconstruct($maxFailedLoginCountValue);

        // when
        $result = $FailedLoginCount->hasReachedLockoutThreshold();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン失敗回数が、アカウントロックのしきい値に達していない場合を判定できる()
    {
        // given
        $maxFailedLoginCountValue = 9;
        $FailedLoginCount = FailedLoginCount::reconstruct($maxFailedLoginCountValue);

        // when
        $result = $FailedLoginCount->hasReachedLockoutThreshold();

        // then
        $this->assertFalse($result);
    }
}