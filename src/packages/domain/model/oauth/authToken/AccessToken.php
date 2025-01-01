<?php

namespace packages\domain\model\oauth\authToken;

use InvalidArgumentException;
use packages\domain\model\authenticationAccount\UserId;

class AccessToken extends AuthToken
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('アクセストークンが空です。');
        }
        parent::__construct($value);
    }

    public function userId(): UserId
    {
        $decoded = $this->decodedValue();
        return new UserId($decoded->sub);
    }
}