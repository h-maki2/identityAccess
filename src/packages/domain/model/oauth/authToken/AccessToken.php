<?php

namespace packages\domain\model\oauth\authToken;

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use InvalidArgumentException;

class AccessToken
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('アクセストークンが空です。');
        }
        $this->value = $value;
    }

    public function id(): string
    {
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        $decoded = JWT::decode($this->value, new Key($publicKey, 'RS256'));
        return $decoded->jti;
    }
}