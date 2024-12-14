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
use RuntimeException;

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
        $result = EloquentAuthenticationInformation::find($id->value);

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationInformation($result);
    }

    public function save(AuthenticationInformation $authenticationInformation): void
    {
        EloquentAuthenticationInformation::updateOrCreate(
            ['user_id' => $authenticationInformation->id()->value],
            [
                'email' => $authenticationInformation->email()->value,
                'password' => $authenticationInformation->password()->hashedValue,
                'verification_status' => $authenticationInformation->verificationStatus()->value,
                'failed_login_count' => $authenticationInformation->loginRestriction()->failedLoginCount(),
                'login_restriction_status' => $authenticationInformation->loginRestriction()->loginRestrictionStatus(),
                'next_login_allowed_at' => $authenticationInformation->loginRestriction()->nextLoginAllowedAt()
            ]
        );
    }

    public function delete(UserId $id): void
    {
        $eloquentAuthenticationInformation = EloquentAuthenticationInformation::find($id->value);

        if ($eloquentAuthenticationInformation === null) {
            throw new RuntimeException('認証情報が存在しません。user_id: ' . $id->value);
        }

        $eloquentAuthenticationInformation->delete();
    }

    public function nextUserId(): UserId
    {
        return new UserId(Uuid::uuid7());
    }

    private function toAuthenticationInformation(EloquentAuthenticationInformation $eloquentAuthenticationInformation): AuthenticationInformation
    {
        return AuthenticationInformation::reconstruct(
            new UserId($eloquentAuthenticationInformation->user_id),
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
}