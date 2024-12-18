<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\authenticationAccount as EloquentAuthenticationAccount;
use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\FailedLoginCount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\NextLoginAllowedAt;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\authenticationAccount;
use packages\domain\model\authenticationAccount\LoginRestrictionStatus;
use packages\domain\model\authenticationAccount\VerificationStatus;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class EloquentAuthenticationAccountRepository implements IAuthenticationAccountRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationAccount
    {
        $result = EloquentAuthenticationAccount::where('email', $email->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationAccount($result);
    }

    public function findById(UserId $id): ?AuthenticationAccount
    {
        $result = EloquentAuthenticationAccount::find($id->value);

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationAccount($result);
    }

    public function save(AuthenticationAccount $authenticationAccount): void
    {
        EloquentAuthenticationAccount::updateOrCreate(
            ['user_id' => $authenticationAccount->id()->value],
            [
                'email' => $authenticationAccount->email()->value,
                'password' => $authenticationAccount->password()->hashedValue,
                'verification_status' => $authenticationAccount->verificationStatus()->value,
                'failed_login_count' => $authenticationAccount->loginRestriction()->failedLoginCount(),
                'login_restriction_status' => $authenticationAccount->loginRestriction()->loginRestrictionStatus(),
                'next_login_allowed_at' => $authenticationAccount->loginRestriction()->nextLoginAllowedAt()
            ]
        );
    }

    public function delete(UserId $id): void
    {
        $eloquentAuthenticationAccount = EloquentAuthenticationAccount::find($id->value);

        if ($eloquentAuthenticationAccount === null) {
            throw new RuntimeException('認証情報が存在しません。user_id: ' . $id->value);
        }

        $eloquentAuthenticationAccount->delete();
    }

    public function nextUserId(): UserId
    {
        return new UserId(Uuid::uuid7());
    }

    private function toAuthenticationAccount(EloquentAuthenticationAccount $eloquentAuthenticationAccount): AuthenticationAccount
    {
        return AuthenticationAccount::reconstruct(
            new UserId($eloquentAuthenticationAccount->user_id),
            new UserEmail($eloquentAuthenticationAccount->email),
            UserPassword::reconstruct($eloquentAuthenticationAccount->password),
            VerificationStatus::from($eloquentAuthenticationAccount->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($eloquentAuthenticationAccount->failed_login_count),
                LoginRestrictionStatus::from($eloquentAuthenticationAccount->login_restriction_status),
                $eloquentAuthenticationAccount->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($eloquentAuthenticationAccount->next_login_allowed_at)) : null
            )
        );
    }
}