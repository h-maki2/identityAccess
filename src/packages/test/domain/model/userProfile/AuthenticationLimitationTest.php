<?php

use packages\domain\model\userProfile\AuthenticationLimitation;
use packages\domain\model\userProfile\FailedLoginCount;
use packages\domain\model\userProfile\NextLoginAt;
use PHPUnit\Framework\TestCase;

class AuthenticationLimitationTest extends TestCase
{
    public function test_初期化する()
    {
        // given

        // when
        $authenticationLimitation = AuthenticationLimitation::initialization();

        // then
        // ログイン失敗回数は0回である
        $this->assertEquals(0, $authenticationLimitation->failedLoginCount());
        // 再ログイン可能な日時はnull
        $this->assertEquals(null, $authenticationLimitation->nextLoginAt());
    }

    public function test_ログイン失敗回数を更新する()
    {
        // given
        $authenticationLimitation = AuthenticationLimitation::reconstruct(
            FailedLoginCount::reconstruct(0),
            null
        );

        // when
        $authenticationLimitationAfterChange = $authenticationLimitation->updateFailedLoginCount();

        // then
        $this->assertEquals(1, $authenticationLimitationAfterChange->failedLoginCount());
        $this->assertEquals(null, $authenticationLimitationAfterChange->nextLoginAt());

        // 元の値は更新されていないことを確認する
        $this->assertEquals(0, $authenticationLimitation->failedLoginCount());
        $this->assertEquals(null, $authenticationLimitation->nextLoginAt());
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達した場合を判定できる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $authenticationLimitation = AuthenticationLimitation::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );

        // when
        $result = $authenticationLimitation->hasReachedAccountLockoutThreshold();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達していない場合を判定できる()
    {
        // given
        // ログイン失敗回数が10回未満の場合
        $authenticationLimitation = AuthenticationLimitation::reconstruct(
            FailedLoginCount::reconstruct(9),
            null
        );

        // when
        $result = $authenticationLimitation->hasReachedAccountLockoutThreshold();

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達した場合、再ログイン可能な日時を設定できる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $authenticationLimitation = AuthenticationLimitation::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );
        $expectedNextLoginAt = NextLoginAt::create();

        // when
        $authenticationLimitationAfterChange = $authenticationLimitation->updateNextLoginAt();

        // then
        $this->assertEquals(10, $authenticationLimitationAfterChange->failedLoginCount());
        // 再ログイン可能な日時が設定されている
        $this->assertEquals($expectedNextLoginAt->formattedValue(), $authenticationLimitationAfterChange->nextLoginAt());

        // 元の値は変更されていないことを確認
        $this->assertEquals(10, $authenticationLimitation->failedLoginCount());
        $this->assertEquals(null, $authenticationLimitation->nextLoginAt());
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達していない場合、再ログイン可能な日時を設定すると例外が発生する()
    {
        // given
        $authenticationLimitation = AuthenticationLimitation::reconstruct(
            FailedLoginCount::reconstruct(9),
            null
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ログイン失敗回数がアカウントロックの回数に達していません。');
        $authenticationLimitation->updateNextLoginAt();
    }
}