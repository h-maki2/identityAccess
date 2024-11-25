<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\FailedLoginCount;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\NextLoginAllowedAt;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\LoginRestrictionStatus;
use packages\domain\model\authenticationInformation\VerificationStatus;
use Ramsey\Uuid\Uuid;

class EloquentAuthenticationInformationRepository implements IAuthenticationInformationRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationInformation
    {
        $result = EloquentAuthenticationInformation::where('email', $email->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationInformation($result);
    }

    public function findById(UserId $id): ?AuthenticationInformation
    {
        $result = $this->eloquentAuthenticationInformationFrom($id);

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationInformation($result);
    }

    public function save(AuthenticationInformation $authenticationInformation): void
    {
        $eloquentAuthenticationInformation = $this->toEloquentAuthenticationInformation($authenticationInformation);
        $eloquentAuthenticationInformation->save();
    }

    public function delete(UserId $id): void
    {
        $eloquentAuthenticationInformation = $this->eloquentAuthenticationInformationFrom($id);

        if ($eloquentAuthenticationInformation === null) {
            // 例外を発生させる予定
        }

        $eloquentAuthenticationInformation->delete();
    }

    public function nextUserId(): UserId
    {
        return new UserId(new IdentifierFromUUIDver7(), Uuid::uuid7());
    }

    private function toAuthenticationInformation(EloquentAuthenticationInformation $eloquentAuthenticationInformation): AuthenticationInformation
    {
        return AuthenticationInformation::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $eloquentAuthenticationInformation->user_id),
            new UserEmail($eloquentAuthenticationInformation->email),
            UserPassword::reconstruct($eloquentAuthenticationInformation->password),
            VerificationStatus::from($eloquentAuthenticationInformation->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($eloquentAuthenticationInformation->failed_login_count),
                LoginRestrictionStatus::from($eloquentAuthenticationInformation->login_restriction_status),
                $eloquentAuthenticationInformation->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($eloquentAuthenticationInformation->next_login_allowed_at)) : null
            )
        );
    }

    private function toEloquentAuthenticationInformation(AuthenticationInformation $authenticationInformation): EloquentAuthenticationInformation
    {
        $eloquentAuthenticationInformation = $this->eloquentAuthenticationInformationFrom($authenticationInformation->id());

        if ($eloquentAuthenticationInformation === null) {
            $eloquentAuthenticationInformation = new EloquentAuthenticationInformation();
            $eloquentAuthenticationInformation->user_id = $authenticationInformation->id()->value;
        }

        $eloquentAuthenticationInformation->email = $authenticationInformation->email()->value;
        $eloquentAuthenticationInformation->password = $authenticationInformation->password()->hashedValue;
        $eloquentAuthenticationInformation->verification_status = $authenticationInformation->verificationStatus()->value;
        $eloquentAuthenticationInformation->failed_login_count = $authenticationInformation->loginRestriction()->failedLoginCount();
        $eloquentAuthenticationInformation->login_restriction_status = $authenticationInformation->loginRestriction()->loginRestrictionStatus();
        $eloquentAuthenticationInformation->next_login_allowed_at = $authenticationInformation->loginRestriction()->nextLoginAllowedAt();

        return $eloquentAuthenticationInformation;        
    }

    private function eloquentAuthenticationInformationFrom(UserId $userId): ?EloquentAuthenticationInformation
    {
        return EloquentAuthenticationInformation::find($userId->value);
    }
}