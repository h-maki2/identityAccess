<?php

use packages\domain\service\common\token\FetchElapsedTimeFromUUIDver7;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

class FetchElapsedTimeFromUUIDver7Test extends TestCase
{
    public function test_UUIDver7が生成されてからの経過時間を正しく取得できる()
    {
        // given
        $uuidVer7 = Uuid::uuid7();
        
        // 現在から14時間後を取得
        $dateTime = new DateTime();
        $interval = new DateInterval('PT14H');
        $dateTime->add($interval);

        $fetchElapsedTime = new FetchElapsedTimeFromUUIDver7();

        // when
        $elapsedTime = $fetchElapsedTime->handle($uuidVer7, $dateTime);

        // then
        $this->assertEquals(14, $elapsedTime);
    }
}