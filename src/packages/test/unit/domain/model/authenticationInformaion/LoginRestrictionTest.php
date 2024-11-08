<?php

use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\LoginRestrictionStatus;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
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
        $loginRestrictionAfterChange = $loginRestriction->updateFailedLoginCount();

        // then
        $this->assertEquals(1, $loginRestrictionAfterChange->failedLoginCount());

        // 元の値は更新されていないことを確認する
        $this->assertEquals(0, $loginRestriction->failedLoginCount());
        $this->assertEquals(null, $loginRestriction->nextLoginAllowedAt());
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $loginRestriction->loginRestrictionStatus());
    }

    public function test_ログイン失敗回数がログイン制限回数に達した場合、ログイン制限が適用可能かどうかを判定できる()
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

    public function test_ログイン失敗回数がログイン制限回数に達していない場合、ログイン制限が適用可能ではないことを判定できる()
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

    public function test_ログイン制限が有効であることを判定できる_再ログイン可能ではない場合()
    {
        // given
        // 再ログイン可能な日時が10分後の場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );

        // when
        $result = $loginRestriction->isEnable(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限が有効ではないことを判定できる_再ログインが可能である場合()
    {
        // given
        // 再ログイン可能な日時が10分前の場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-10 minutes'))
        );

        // when
        $result = $loginRestriction->isEnable(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が有効ではないことを判定できる_ログイン制限されていない場合()
    {
        // given
        // ログイン制限されていない場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );

        // when
        $result = $loginRestriction->isEnable(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が適用可能である場合、ログイン制限を有効にできる()
    {
        // given
        // ログイン失敗回数が10回に達した場合で、すでにログイン制限が適用されていない場合
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

    public function test_ログイン制限が適用可能ではない場合、ログイン制限を有効にできない()
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

    public function test_すでにログイン制限が有効である場合、ログイン制限を有効にできない()
    {
        // given
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("すでにログイン制限が有効です。");
        $loginRestriction->enable(new DateTimeImmutable());
    }

    public function test_再ログイン可能である場合に、ログイン制限を無効にできる()
    {
        // given
        // 再ログイン可能である場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-10 minutes'))
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

    public function test_再ログイン可能ではない場合に、ログイン制限を無効にできない()
    {
        // given
        // 再ログイン可能でない場合
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("ログイン制限の期間内です。");
        $loginRestriction->disable(new DateTimeImmutable());
    }

    public function test_ログイン制限が有効ではない場合に、ログイン制限を無効化できない()
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
        $this->expectExceptionMessage("ログイン制限が有効ではありません。");
        $loginRestriction->disable(new DateTimeImmutable());
    }
}