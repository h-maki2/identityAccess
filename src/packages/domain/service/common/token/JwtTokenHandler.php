<?php

namespace packages\domain\service\common\tokne;

use RuntimeException;

abstract class JwtTokenHandler
{
    /**
     * JWTトークンを生成する
     */
    abstract public function encode(string $id, int $expirationTime, string $secretKeyPath): string;

    abstract protected function payload(string $id, int $expirationTime): array;
}