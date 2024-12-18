<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\DefinitiveRegistrationConfirmation as EloquentDefinitiveRegistrationConfirmation;
use DateTimeImmutable;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UserId;

class EloquentDefinitiveRegistrationConfirmationRepository implements IDefinitiveRegistrationConfirmationRepository
{
    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?DefinitiveRegistrationConfirmation
    {
        $result = EloquentDefinitiveRegistrationConfirmation::where('one_time_token_value', $tokenValue->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toDefinitiveRegistrationConfirmation($result);
    }

    public function findById(UserId $userId): ?DefinitiveRegistrationConfirmation
    {
        $eloquentDefinitiveRegistrationConfirmation = EloquentDefinitiveRegistrationConfirmation::find($userId->value);

        if ($eloquentDefinitiveRegistrationConfirmation === null) {
            return null;
        }

        return $this->toDefinitiveRegistrationConfirmation($eloquentDefinitiveRegistrationConfirmation);
    }

    public function save(DefinitiveRegistrationConfirmation $definitiveRegistrationConfirmation): void
    {
        EloquentDefinitiveRegistrationConfirmation::updateOrCreate(
            ['user_id' => $definitiveRegistrationConfirmation->userId->value],
            [
                'one_time_token_value' => $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value,
                'one_time_token_expiration' => $definitiveRegistrationConfirmation->oneTimeToken()->expirationDate(),
                'one_time_password' => $definitiveRegistrationConfirmation->oneTimePassword()->value
            ]
        );
    }

    public function delete(UserId $id): void
    {
        $eloquentDefinitiveRegistrationConfirmation = EloquentDefinitiveRegistrationConfirmation::find($id->value);
        $eloquentDefinitiveRegistrationConfirmation->delete();
    }

    private function toDefinitiveRegistrationConfirmation(object $eloquentDefinitiveRegistrationConfirmation): DefinitiveRegistrationConfirmation
    {
        return DefinitiveRegistrationConfirmation::reconstruct(
            new UserId($eloquentDefinitiveRegistrationConfirmation->user_id),
            OneTimeToken::reconstruct(
                OneTimeTokenValue::reconstruct($eloquentDefinitiveRegistrationConfirmation->one_time_token_value),
                OneTimeTokenExpiration::reconstruct(new DateTimeImmutable($eloquentDefinitiveRegistrationConfirmation->one_time_token_expiration))
            ),
            OneTimePassword::reconstruct($eloquentDefinitiveRegistrationConfirmation->one_time_password)
        );
    }
}