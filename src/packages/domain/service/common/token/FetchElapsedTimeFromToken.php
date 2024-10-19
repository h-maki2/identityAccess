<?php

namespace packages\domain\service\common\token;

use DateTime;

interface FetchElapsedTimeFromToken
{
    /**
     * トークンが生成されてからの経過時間を取得する
     */
    public function handle(string $token, DateTime $today): int;
}