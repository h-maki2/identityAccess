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
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

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
        $result = $this->eloquentAuthConfirmationFrom($userId);

        if ($result === null) {
            return null;
        }

        return $this->toAuthConfirmation($result);
    }

    public function save(AuthConfirmation $authConfirmation): void
    {
        $eloquentAuthConfirmation = $this->toEloquentAuthConfirmation($authConfirmation);
        $eloquentAuthConfirmation->save();
    }

    public function delete(AuthConfirmation $authConfirmation): void
    {
        $eloquentAuthConfirmation = $this->eloquentAuthConfirmationFrom($authConfirmation->userId);
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

    private function toEloquentAuthConfirmation(AuthConfirmation $authConfirmation): EloquentAuthConfirmation
    {
        $eloquentAuthConfirmation = $this->eloquentAuthConfirmationFrom($authConfirmation->userId);

        if ($eloquentAuthConfirmation === null) {
            $eloquentAuthConfirmation = new EloquentAuthConfirmation();
            $eloquentAuthConfirmation->user_id = $authConfirmation->userId->value;
        }
        $eloquentAuthConfirmation->one_time_token_value = $authConfirmation->oneTimeToken()->value();
        $eloquentAuthConfirmation->one_time_token_expiration = $authConfirmation->oneTimeToken()->expirationDate();
        $eloquentAuthConfirmation->one_time_password = $authConfirmation->oneTimePassword()->value;

        return $eloquentAuthConfirmation;
    }

    private function eloquentAuthConfirmationFrom(UserId $userId): ?EloquentAuthConfirmation
    {
        return EloquentAuthConfirmation::find($userId->value);
    }
}