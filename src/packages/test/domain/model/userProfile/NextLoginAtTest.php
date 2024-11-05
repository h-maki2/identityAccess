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
        $this->assertEquals($expectedDateTimeString, $nextLoginAt->value()->format('Y-m-d HH:MM'));
    }
}