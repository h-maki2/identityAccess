<?php

use PHPUnit\Framework\TestCase;

use DateTimeImmutable;
use packages\domain\model\authenticationAccount\NextLoginAllowedAt;

class NextLoginAllowedAtTest extends TestCase
{
    public function test_再ログイン可能な日時は現在の時刻から10分後()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        $expectedDateTime = $currentDateTime->add(new DateInterval('PT10M'));
        $expectedDateTimeString = $expectedDateTime->format('Y-m-d H:i');

        // when
        $nextLoginAllowedAt = NextLoginAllowedAt::create();

        // then
        $this->assertEquals($expectedDateTimeString, $nextLoginAllowedAt->formattedValue());
    }

    public function test_再ログインが可能である場合を判定できる()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        // 現在の日時から10分後は再ログインが可能
        $再ログイン可能な日時 = $currentDateTime->add(new DateInterval('PT10M01S'));
        $nextLoginAllowedAt = NextLoginAllowedAt::create();

        // when
        $result = $nextLoginAllowedAt->isAvailable($再ログイン可能な日時);

        // then
        $this->assertTrue($result);
    }

    public function test_再ログインが可能ではない場合を判定できる()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        // 現在の日時から9分は再ログインが可能ではない
        $再ログインが可能ではない日時 = $currentDateTime->add(new DateInterval('PT09M'));
        $nextLoginAllowedAt = NextLoginAllowedAt::create();

        // when
        $result = $nextLoginAllowedAt->isAvailable($再ログインが可能ではない日時);

        // then
        $this->assertFalse($result);
    }
}