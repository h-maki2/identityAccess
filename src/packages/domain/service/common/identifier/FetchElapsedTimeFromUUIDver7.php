<?php

namespace packages\domain\service\common\identifier;

use DateTime;

class FetchElapsedTimeFromUUIDver7 implements FetchElapsedTimeFromIdentifier
{
    /**
     * UUIDver7が生成されてからの経過時間（h）を取得
     */
    public function handle(string $uuidVer7, DateTime $today): int
    {
        $uuidVer7WithoutHyphens = $this->removeHyphens($uuidVer7);
        $timestampHex = $this->timestampHexFromUUIDver7($uuidVer7WithoutHyphens);
        $timestampSecond = $this->conversionTimestampFromHexToSeconds($timestampHex);
        $elapsedSeconds = $this->elapsedSecondsFrom($timestampSecond, $today);
        return $this->conversionElapsedSecondToHours($elapsedSeconds);
    }

    private function removeHyphens(string $uuidVer7): string
    {
        return str_replace('-', '', $uuidVer7);
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
    private function conversionTimestampFromHexToSeconds(string $timestampHex): float
    {
        // 10進数に変換する
        $timestampMs = hexdec($timestampHex);
        return $timestampMs / 1000;
    }

    /**
     * UUIDver7が生成されてからの経過時間を取得
     */
    private function elapsedSecondsFrom(float $timestampSecond, DateTime $today): float
    {
        $currentTimestamp = $today->getTimestamp();
        return $currentTimestamp - $timestampSecond;
    }

    /**
     * 秒から時間に変換
     */
    private function conversionElapsedSecondToHours(float $elapsedSeconds): int
    {
        return ceil($elapsedSeconds / 3600);
    }
}