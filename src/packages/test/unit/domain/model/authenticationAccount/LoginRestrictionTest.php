<?php

use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\FailedLoginCount;
use packages\domain\model\authenticationAccount\LoginRestrictionStatus;
use packages\domain\model\authenticationAccount\NextLoginAllowedAt;
use PHPUnit\Framework\TestCase;

class LoginRestrictionTest extends TestCase
{
    public function test_初期化する()
    {
        // given

        // when
        $loginRestriction = LoginRestriction::initialization();

        // then
        // ログイン失敗回数は0回である
        $this->assertEquals(0, $loginRestriction->failedLoginCount());
        // 再ログイン可能な日時はnull
        $this->assertEquals(null, $loginRestriction->nextLoginAllowedAt());
        // ログイン制限ステータスは制限なし
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $loginRestriction->loginRestrictionStatus());
    }

    public function test_ログイン失敗回数を更新する()
    {
        // given
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $loginRestrictionAfterChange = $loginRestriction->addFailedLoginCount();

        // then
        $this->assertEquals(1, $loginRestrictionAfterChange->failedLoginCount());

        // 元の値は更新されていないことを確認する
        $this->assertEquals(0, $loginRestriction->failedLoginCount());
        $this->assertEquals(null, $loginRestriction->nextLoginAllowedAt());
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $loginRestriction->loginRestrictionStatus());
    }

    public function test_ログイン制限が有効可能であることを判定できる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $result = $loginRestriction->canApply();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限を有効にできないことを判定できる()
    {
        // given
        // ログイン失敗回数が10回未満の場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $result = $loginRestriction->canApply();

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が有効で再ログインが可能である場合、ログイン制限を無効にできることを判定できる()
    {
        // given
        // 現在ログイン制限が有効で再ログイン可能である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );

        // when
        $result = $loginRestriction->canDisable(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限が有効だが再ログインが不可の場合、ログイン制限を無効にできないことを判定できる()
    {
        // given
        // 現在ログイン制限が有効で再ログイン不可である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+1 minutes'))
        );

        // when
        $result = $loginRestriction->canDisable(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が有効ではない場合、ログイン制限を無効にできないことを判定できる()
    {
        // given
        // 現在ログイン制限が有効でない場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $result = $loginRestriction->canDisable(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が有効可能である場合、ログイン制限を有効にできる()
    {
        // given
        // ログイン失敗回数が10回に達した場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $loginRestrictionAfterChange = $loginRestriction->enable(new DateTimeImmutable());

        // then
        $this->assertEquals(LoginRestrictionStatus::Restricted->value, $loginRestrictionAfterChange->loginRestrictionStatus());
        $this->assertNotNull($loginRestrictionAfterChange->nextLoginAllowedAt());
        $this->assertEquals(10, $loginRestrictionAfterChange->failedLoginCount());

        // 元のインスタンスは更新されていないことを確認する
        $this->assertEquals(10, $loginRestriction->failedLoginCount());
        $this->assertEquals(null, $loginRestriction->nextLoginAllowedAt());
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $loginRestriction->loginRestrictionStatus());
    }

    public function test_ログイン制限が有効可能ではない場合、ログイン制限を有効にできない()
    {
        // given
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("ログイン失敗回数がログイン制限の回数に達していません。");
        $loginRestriction->enable(new DateTimeImmutable());
    }

    public function test_ログイン制限が有効状態で尚且つ再ログインが可能である場合、ログイン制限を無効にできる()
    {
        // given
        // ログイン制限が有効で再ログインが可能である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );

        // when
        $loginRestrictionAfterChange = $loginRestriction->disable(new DateTimeImmutable());

        // then
        // ログイン制限が無効になっていて、ログイン失敗回数が0回にリセットされていることを確認する
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $loginRestrictionAfterChange->loginRestrictionStatus());
        $this->assertNull($loginRestrictionAfterChange->nextLoginAllowedAt());
        $this->assertEquals(0, $loginRestrictionAfterChange->failedLoginCount());

        // 元のインスタンスは更新されていないことを確認する
        $this->assertEquals(10, $loginRestriction->failedLoginCount());
        $this->assertEquals(LoginRestrictionStatus::Restricted->value, $loginRestriction->loginRestrictionStatus());
        $this->assertNotNull($loginRestriction->nextLoginAllowedAt());
    }

    public function test_ログイン制限が有効状態で尚且つ再ログインが不可である場合、ログイン制限を無効にできない()
    {
        // given
        // ログイン制限が現在有効状態で尚且つ再ログインが不可である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+1 minutes'))
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("ログイン制限が有効ではないか、もしくはログイン制限の期間内です。");
        $loginRestriction->disable(new DateTimeImmutable());
    }

    public function test_ログイン制限が有効状態ではない場合に、ログイン制限を無効化できない()
    {
        // given
        // ログイン制限が有効ではない場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("ログイン制限が有効ではないか、もしくはログイン制限の期間内です。");
        $loginRestriction->disable(new DateTimeImmutable());
    }

    public function test_ログイン制限中かどうかを判定できる()
    {
        // given
        // ログイン制限が有効である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+1 minutes'))
        );

        // when
        $result = $loginRestriction->isRestricted();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限中ではないことを判定できる()
    {
        // given
        // ログイン制限が有効でない場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $result = $loginRestriction->isRestricted();

        // then
        $this->assertFalse($result);
    }
}