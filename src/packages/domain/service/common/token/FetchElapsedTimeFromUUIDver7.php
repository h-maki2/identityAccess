<?php

namespace packages\domain\service\common\token;

use DateTime;

class FetchElapsedTimeFromUUIDver7 implements FetchElapsedTimeFromToken
{
    /**
     * UUIDver7が生成されてからの経過時間を取得
     */
    public function handle(string $uuidVer7, DateTime $today): int
    {
        $timestampHex = $this->timestampHexFromUUIDver7($uuidVer7);
        $timestampSecond = $this->conversionfromHexToSeconds($timestampHex);
        $elapsedSeconds = $this->elapsedSecondsFrom($timestampSecond, $today);
        return $this->conversionfromSecondsToHours($elapsedSeconds);
    }

    /**
     * 16進数のタイムスタンプをUUIDver7から取得する
     */
    private function timestampHexFromUUIDver7(string $uuidVer7): string
    {
        return substr($uuidVer7, 0, 12);
    }

    /**
     * 16進数のタイムスタンプを秒単位に変換
     */
    private function conversionfromHexToSeconds(string $timestampHex): int
    {
        // 10進数に変換する
        $timestampMs = hexdec($timestampHex);
        return $timestampMs / 1000;
    }

    /**
     * UUIDver7が生成されてからの経過時間を取得
     */
    private function elapsedSecondsFrom(int $timestampSecond, DateTime $today): int
    {
        $currentTimestamp = $today->getTimestamp();
        return $currentTimestamp - $timestampSecond;
    }

    /**
     * 秒から時間に変換
     */
    private function conversionfromSecondsToHours(int $elapsedSeconds): int
    {
        return floor($elapsedSeconds / 3600);
    }
}