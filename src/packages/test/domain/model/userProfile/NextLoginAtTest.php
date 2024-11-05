<?php

use PHPUnit\Framework\TestCase;

use DateTimeImmutable;
use packages\domain\model\userProfile\NextLoginAt;

class NextLoginAtTest extends TestCase
{
    public function test_再ログイン可能な日時は現在の時刻から10分後()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        $expectedDateTime = $currentDateTime->add(new DateInterval('PT10M'));
        $expectedDateTimeString = $expectedDateTime->format('Y-m-d HH:MM');

        // when
        $nextLoginAt = NextLoginAt::create();

        // then
        $this->assertEquals($expectedDateTimeString, $nextLoginAt->formattedValue());
    }

    public function test_再ログインが可能である場合を判定できる()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        // 現在の日時から10分後は再ログインが可能
        $再ログイン可能な日時 = $currentDateTime->add(new DateInterval('PT10M01S'));
        $nextLoginAt = NextLoginAt::create();

        // when
        $result = $nextLoginAt->isAvailable($再ログイン可能な日時);

        // then
        $this->assertTrue($result);
    }

    public function test_再ログインが可能ではない場合を判定できる()
    {
        // given
        $currentDateTime = new DateTimeImmutable();
        // 現在の日時から9分は再ログインが可能ではない
        $再ログインが可能ではない日時 = $currentDateTime->add(new DateInterval('PT09M'));
        $nextLoginAt = NextLoginAt::create();

        // when
        $result = $nextLoginAt->isAvailable($再ログインが可能ではない日時);

        // then
        $this->assertFalse($result);
    }
}