<?php

namespace packages\domain\model\oauth\client;

enum ResponseType: string
{
    case Code = 'code';

    public function isCode(): bool
    {
        return $this === self::Code;
    }
}