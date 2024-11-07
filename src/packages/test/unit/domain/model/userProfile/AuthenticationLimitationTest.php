<?php

use packages\domain\model\userProfile\LoginRestriction;
use packages\domain\model\userProfile\FailedLoginCount;
use packages\domain\model\userProfile\NextLoginAt;
use PHPUnit\Framework\TestCase;

class LoginRestrictionTest extends TestCase
{
    public function test_初期化する()
    {
        // given

        // when
        $LoginRestriction = LoginRestriction::initialization();

        // then
        // ログイン失敗回数は0回である
        $this->assertEquals(0, $LoginRestriction->failedLoginCount());
        // 再ログイン可能な日時はnull
        $this->assertEquals(null, $LoginRestriction->nextLoginAt());
    }

    public function test_ログイン失敗回数を更新する()
    {
        // given
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            null
        );

        // when
        $LoginRestrictionAfterChange = $LoginRestriction->updateFailedLoginCount();

        // then
        $this->assertEquals(1, $LoginRestrictionAfterChange->failedLoginCount());
        $this->assertEquals(null, $LoginRestrictionAfterChange->nextLoginAt());

        // 元の値は更新されていないことを確認する
        $this->assertEquals(0, $LoginRestriction->failedLoginCount());
        $this->assertEquals(null, $LoginRestriction->nextLoginAt());
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達した場合を判定できる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );

        // when
        $result = $LoginRestriction->hasReachedAccountLockoutThreshold();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達していない場合を判定できる()
    {
        // given
        // ログイン失敗回数が10回未満の場合
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            null
        );

        // when
        $result = $LoginRestriction->hasReachedAccountLockoutThreshold();

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達した場合、再ログイン可能な日時を設定できる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );
        $expectedNextLoginAt = NextLoginAt::create();

        // when
        $LoginRestrictionAfterChange = $LoginRestriction->updateNextLoginAt();

        // then
        $this->assertEquals(10, $LoginRestrictionAfterChange->failedLoginCount());
        // 再ログイン可能な日時が設定されている
        $this->assertEquals($expectedNextLoginAt->formattedValue(), $LoginRestrictionAfterChange->nextLoginAt());

        // 元の値は変更されていないことを確認
        $this->assertEquals(10, $LoginRestriction->failedLoginCount());
        $this->assertEquals(null, $LoginRestriction->nextLoginAt());
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達していない場合、再ログイン可能な日時を設定すると例外が発生する()
    {
        // given
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            null
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ログイン失敗回数がアカウントロックの回数に達していません。');
        $LoginRestriction->updateNextLoginAt();
    }
}