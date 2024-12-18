<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use InvalidArgumentException;
use packages\domain\model\definitiveRegistrationConfirmation\validation\OneTimeTokenValueValidation;

class OneTimeTokenValue
{
    readonly string $value;

    private function __construct(string $value)
    {
        if (!OneTimeTokenValueValidation::validate($value)) {
            throw new InvalidArgumentException('Invalid token value');
        }

        $this->value = $value;
    }

    public static function create(): self
    {
        return new self(bin2hex(random_bytes(OneTimeTokenValueValidation::tokenValueLength() / 2)));
    }

    public static function reconstruct(string $value): self
    {
        return new self($value);
    }
}