<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use InvalidArgumentException;
use packages\domain\model\definitiveRegistrationConfirmation\validation\OneTimePasswordValidation;

class OneTimePassword
{
    readonly string $value;

    private function __construct(string $value)
    {
        if (!OneTimePasswordValidation::validate($value)) {
            throw new InvalidArgumentException('Invalid password value');
        }

        $this->value = $value;
    }

    public static function create(): self
    {
        return new self((string)random_int(100000, 999999));
    }

    public static function reconstruct(string $value): self
    {
        return new self($value);
    }

    public function equals(OneTimePassword $other): bool
    {
        return $this->value === $other->value;
    }
}