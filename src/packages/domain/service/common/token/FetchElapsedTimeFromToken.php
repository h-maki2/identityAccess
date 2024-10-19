<?php

namespace packages\domain\service\common\hash;

interface FetchElapsedTimeFromToken
{
    public function handle(string $token): int;
}