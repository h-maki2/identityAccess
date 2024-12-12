<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class InMemoryAuthConfirmationRepository implements IAuthConfirmationRepository
{
    private array $authConfirmationsObjList = [];

    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?AuthConfirmation
    {
        foreach ($this->authConfirmationsObjList as $authConfirmationsObj) {
            if ($authConfirmationsObj->one_time_token === $tokenValue->value) {
                return $this->toAuthConfirmation($authConfirmationsObj);
            }
        }

        return null;
    }

    public function findById(UserId $userId): ?AuthConfirmation
    {
        if (!isset($this->authConfirmationsObjList[$userId->value])) {
            return null;
        }

        return $this->toAuthConfirmation($this->authConfirmationsObjList[$userId->value]);
    }

    public function save(AuthConfirmation $authConfirmation): void
    {
        $this->authConfirmationsObjList[$authConfirmation->userId->value] = (object) [
            'user_id' => $authConfirmation->userId->value,
            'one_time_token' => $authConfirmation->oneTimeToken()->value(),
            'one_time_token_expiration' => $authConfirmation->oneTimeToken()->expirationDate(),
            'one_time_password' => $authConfirmation->oneTimePassword()->value
        ];
    }

    public function delete(UserId $id): void
    {
        unset($this->authConfirmationsObjList[$id->value]);
    }

    private function toAuthConfirmation(object $authConfirmationsObj): AuthConfirmation
    {
        return AuthConfirmation::reconstruct(
            new UserId($authConfirmationsObj->user_id),
            OneTimeToken::reconstruct(
                OneTimeTokenValue::reconstruct($authConfirmationsObj->one_time_token),
                OneTimeTokenExpiration::reconstruct(new DateTimeImmutable($authConfirmationsObj->one_time_token_expiration))
            ),
            OneTimePassword::reconstruct($authConfirmationsObj->one_time_password)
        );
    }
}