<?php

namespace packages\adapter\token\FirebaseJwtCreator;

use packages\domain\service\common\tokne\JwtCreator;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

class FirebaseJwtCreator extends JwtCreator
{

    private const ENCRYPTION_ALGORITHM = 'HS256';

    /**
     * JWTトークンを生成する
     */
    public function encode(string $id, int $expirationTime): string
    {
        return JWT::encode($this->payload($id, $expirationTime), $this->secretKey(), self::ENCRYPTION_ALGORITHM);
    }

    /**
     * JWTトークンをデコードする
     */
    public function decode(string $jwtToken): string
    {
        $decodedToken = JWT::decode($jwtToken, new Key($this->secretKey(), self::ENCRYPTION_ALGORITHM));
        return $decodedToken->sub;
    }

    protected function payload(string $id, int $expirationTime): array
    {
        return [
            'iss' => config('app.app_domain'),
            'aud' => config('app.app_domain'),
            'exp' => $expirationTime,
            'iat' => time(),
            'sub' => $id
        ];
    }
}