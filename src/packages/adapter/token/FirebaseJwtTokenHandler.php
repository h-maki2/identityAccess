<?php

namespace packages\adapter\token;

use packages\domain\service\common\tokne\JwtTokenHandler;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

class FirebaseJwtTokenHandler extends JwtTokenHandler
{

    private const ENCRYPTION_ALGORITHM = 'HS256';

    /**
     * JWTトークンを生成する
     */
    public function encode(string $id, int $expirationTime, string $secretKeyPath): string
    {
        return JWT::encode($this->payload($id, $expirationTime), $secretKeyPath, self::ENCRYPTION_ALGORITHM);
    }

    protected function payload(string $id, int $expirationTime): array
    {
        return [
            'iss' => config('app.app_domain'),
            'aud' => config('app.app_domain'),
            'exp' => $expirationTime,
            'iat' => time(), // 発行時間
            'nbf' => time(), // 有効開始時間
            'sub' => $id
        ];
    }
}