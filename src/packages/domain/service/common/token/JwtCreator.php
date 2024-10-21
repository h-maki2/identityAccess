<?php

namespace packages\domain\service\common\tokne;

use RuntimeException;

abstract class JwtCreator
{
    /**
     * JWTトークンを生成する
     */
    abstract public function encode(string $id, int $expirationTime): string;

    /**
     * JWTトークンをデコードする
     */
    abstract public function decode(string $jwtToken): string;

    abstract protected function payload(string $id, int $expirationTime): array;

    protected function secretKey(): string
    {
        $secretKey = getenv('JWT_SECRET_KEY');
        if (!$secretKey) {
            throw new RuntimeException('秘密鍵が取得できません。');
        }
        return $secretKey;
    }
}