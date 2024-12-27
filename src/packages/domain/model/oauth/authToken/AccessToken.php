<?php

namespace packages\domain\model\oauth\authToken;

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use packages\domain\model\authenticationAccount\UserId;
use stdClass;

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
        $decoded = $this->decodedValue();
        return $decoded->jti;
    }

    public function userId(): UserId
    {
        $decoded = $this->decodedValue();
        return new UserId($decoded->sub);
    }

    private function decodedValue(): stdClass
    {
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        return JWT::decode($this->value, new Key($publicKey, 'RS256'));
    }
}