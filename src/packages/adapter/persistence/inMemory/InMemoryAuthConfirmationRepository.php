<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class InMemoryAuthConfirmationRepository implements IAuthConfirmationRepository
{
    private array $authConfirmationsObjList = [];

    public function findByToken(OneTimeToken $token): ?AuthConfirmation
    {
        foreach ($this->authConfirmationsObjList as $authConfirmationsObj) {
            if ($authConfirmationsObj->one_time_token === $token->value) {
                return $this->toAuthConfirmation($authConfirmationsObj);
            }
        }

        return null;
    }

    public function save(AuthConfirmation $authConfirmation): void
    {
        $this->authConfirmationsObjList[$authConfirmation->userId->value] = (object) [
            'user_id' => $authConfirmation->userId->value,
            'one_time_token' => $authConfirmation->oneTimeToken()->value,
            'one_time_token_expiration' => $authConfirmation->oneTimeToken()->expiration()->formattedValue(),
            'one_time_password' => $authConfirmation->oneTimePassword()->value
        ];
    }

    public function delete(OneTimeToken $token): void
    {
        foreach ($this->authConfirmationsObjList as $key => $authConfirmationsObj) {
            if ($authConfirmationsObj->one_time_token === $token->value) {
                unset($this->authConfirmationsObjList[$key]);
            }
        }
    }

    private function toAuthConfirmation(object $authConfirmationsObj): AuthConfirmation
    {
        return AuthConfirmation::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $authConfirmationsObj->user_id),
            new OneTimeToken(
                $authConfirmationsObj->one_time_token,
                OneTimeTokenExpiration::reconstruct(new DateTimeImmutable($authConfirmationsObj->one_time_token_expiration))
            ),
            new OneTimePassword($authConfirmationsObj->one_time_password)
        );
    }
}