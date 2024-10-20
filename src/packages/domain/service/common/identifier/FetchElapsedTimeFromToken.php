<?php

namespace packages\domain\service\common\identifier;

use DateTime;

interface FetchElapsedTimeFromIdentifier
{
    /**
     * トークンが生成されてからの経過時間を取得する
     */
    public function handle(string $token, DateTime $today): int;
}