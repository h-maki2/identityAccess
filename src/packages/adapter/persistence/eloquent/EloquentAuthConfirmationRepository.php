<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\AuthConfirmation as EloquentAuthConfirmation;
use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UserId;

class EloquentAuthConfirmationRepository implements IAuthConfirmationRepository
{
    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?AuthConfirmation
    {
        $result = EloquentAuthConfirmation::where('one_time_token_value', $tokenValue->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toAuthConfirmation($result);
    }

    public function findById(UserId $userId): ?AuthConfirmation
    {
        $eloquentAuthConfirmation = EloquentAuthConfirmation::find($userId->value);

        if ($eloquentAuthConfirmation === null) {
            return null;
        }

        return $this->toAuthConfirmation($eloquentAuthConfirmation);
    }

    public function save(AuthConfirmation $authConfirmation): void
    {
        EloquentAuthConfirmation::updateOrCreate(
            ['user_id' => $authConfirmation->userId->value],
            [
                'one_time_token_value' => $authConfirmation->oneTimeToken()->tokenValue()->value,
                'one_time_token_expiration' => $authConfirmation->oneTimeToken()->expirationDate(),
                'one_time_password' => $authConfirmation->oneTimePassword()->value
            ]
        );
    }

    public function delete(UserId $id): void
    {
        $eloquentAuthConfirmation = EloquentAuthConfirmation::find($id->value);
        $eloquentAuthConfirmation->delete();
    }

    private function toAuthConfirmation(object $eloquentAuthConfirmation): AuthConfirmation
    {
        return AuthConfirmation::reconstruct(
            new UserId($eloquentAuthConfirmation->user_id),
            OneTimeToken::reconstruct(
                OneTimeTokenValue::reconstruct($eloquentAuthConfirmation->one_time_token_value),
                OneTimeTokenExpiration::reconstruct(new DateTimeImmutable($eloquentAuthConfirmation->one_time_token_expiration))
            ),
            OneTimePassword::reconstruct($eloquentAuthConfirmation->one_time_password)
        );
    }
}