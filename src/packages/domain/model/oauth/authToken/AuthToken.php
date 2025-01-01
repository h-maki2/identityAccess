<?php

namespace packages\domain\model\oauth\authToken;

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use stdClass;

abstract class AuthToken
{
    readonly string $value;

    protected function __construct(string $value)
    {
        $this->value = $value;
    }

    public function id(): string
    {
        $decoded = $this->decodedValue();
        return $decoded->jti;
    }

    protected function decodedValue(): stdClass
    {
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        return JWT::decode($this->value, new Key($publicKey, 'RS256'));
    }
}