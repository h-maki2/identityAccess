<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UserId;

class InMemoryDefinitiveRegistrationConfirmationRepository implements IDefinitiveRegistrationConfirmationRepository
{
    private array $definitiveRegistrationConfirmationsObjList = [];

    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?DefinitiveRegistrationConfirmation
    {
        foreach ($this->definitiveRegistrationConfirmationsObjList as $definitiveRegistrationConfirmationsObj) {
            if ($definitiveRegistrationConfirmationsObj->one_time_token === $tokenValue->value) {
                return $this->toDefinitiveRegistrationConfirmation($definitiveRegistrationConfirmationsObj);
            }
        }

        return null;
    }

    public function findById(UserId $userId): ?DefinitiveRegistrationConfirmation
    {
        if (!isset($this->definitiveRegistrationConfirmationsObjList[$userId->value])) {
            return null;
        }

        return $this->toDefinitiveRegistrationConfirmation($this->definitiveRegistrationConfirmationsObjList[$userId->value]);
    }

    public function save(DefinitiveRegistrationConfirmation $definitiveRegistrationConfirmation): void
    {
        $this->definitiveRegistrationConfirmationsObjList[$definitiveRegistrationConfirmation->userId->value] = (object) [
            'user_id' => $definitiveRegistrationConfirmation->userId->value,
            'one_time_token' => $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value,
            'one_time_token_expiration' => $definitiveRegistrationConfirmation->oneTimeToken()->expirationDate(),
            'one_time_password' => $definitiveRegistrationConfirmation->oneTimePassword()->value
        ];
    }

    public function delete(UserId $id): void
    {
        unset($this->definitiveRegistrationConfirmationsObjList[$id->value]);
    }

    private function toDefinitiveRegistrationConfirmation(object $definitiveRegistrationConfirmationsObj): DefinitiveRegistrationConfirmation
    {
        return DefinitiveRegistrationConfirmation::reconstruct(
            new UserId($definitiveRegistrationConfirmationsObj->user_id),
            OneTimeToken::reconstruct(
                OneTimeTokenValue::reconstruct($definitiveRegistrationConfirmationsObj->one_time_token),
                OneTimeTokenExpiration::reconstruct(new DateTimeImmutable($definitiveRegistrationConfirmationsObj->one_time_token_expiration))
            ),
            OneTimePassword::reconstruct($definitiveRegistrationConfirmationsObj->one_time_password)
        );
    }
}