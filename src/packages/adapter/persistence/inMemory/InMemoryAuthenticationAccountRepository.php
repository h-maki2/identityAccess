<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\FailedLoginCount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\NextLoginAllowedAt;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\LoginRestrictionStatus;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\UserDefinitiveRegistrationStatus;
use Ramsey\Uuid\Uuid;
use UnexpectedValueException;

class InMemoryAuthenticationAccountRepository implements IAuthenticationAccountRepository
{
    private array $authenticationAccountList = [];

    public function findByEmail(UserEmail $email): ?AuthenticationAccount
    {
        foreach ($this->authenticationAccountList as $authenticationAccountObj) {
            if ($authenticationAccountObj->email === $email->value) {
                return $this->toAuthenticationAccount($authenticationAccountObj);
            }
        }

        return null;
    }

    public function findById(UserId $id, UnsubscribeStatus $unsubscribeStatus): ?AuthenticationAccount
    {
        $authenticationAccountObj = $this->authenticationAccountList[$id->value] ?? null;
        if ($authenticationAccountObj === null) {
            return null;
        }

        if ($authenticationAccountObj->unsubscribe !== $unsubscribeStatus->value) {
            return null;
        }

        return $this->toAuthenticationAccount($authenticationAccountObj);
    }

    public function save(AuthenticationAccount $authenticationAccount): void
    {
        $this->authenticationAccountList[$authenticationAccount->id()->value] = $this->toAuthenticationAccountModel($authenticationAccount);
    }

    public function delete(AuthenticationAccount $authenticationAccount): void
    {
        $authenticationAccountObj = $this->authenticationAccountList[$authenticationAccount->id()->value] ?? null;
        if ($authenticationAccountObj === null) {
            throw new UnexpectedValueException('指定されたIDのユーザーは存在しません。');
        }

        $authenticationAccountObj->unsubscribe = $authenticationAccount->unsubscribeStatus()->value;

        // 疑似的に削除したことにする
        $authenticationAccountObj->email = null;
        $authenticationAccountObj->password = null;
        $authenticationAccountObj->verification_status = null;
        $authenticationAccountObj->failed_login_count = null;
        $authenticationAccountObj->next_login_allowed_at = null;
        $authenticationAccountObj->login_restriction_status = null;
    }

    public function nextUserId(): UserId
    {
        return new UserId(Uuid::uuid7());
    }

    private function toAuthenticationAccount(object $authenticationAccountObj): AuthenticationAccount
    {
        return AuthenticationAccount::reconstruct(
            new UserId($authenticationAccountObj->user_id),
            new UserEmail($authenticationAccountObj->email),
            UserPassword::reconstruct($authenticationAccountObj->password),
            UserDefinitiveRegistrationStatus::from($authenticationAccountObj->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($authenticationAccountObj->failed_login_count),
                LoginRestrictionStatus::from($authenticationAccountObj->login_restriction_status),
                $authenticationAccountObj->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($authenticationAccountObj->next_login_allowed_at)) : null
            ),
            UnsubscribeStatus::from($authenticationAccountObj->unsubscribe)
        );
    }

    private function toAuthenticationAccountModel(AuthenticationAccount $authenticationAccount): object
    {
        return (object) [
            'user_id' => $authenticationAccount->id()->value,
            'email' => $authenticationAccount->email()->value,
            'password' => $authenticationAccount->password()->hashedValue,
            'verification_status' => $authenticationAccount->UserDefinitiveRegistrationStatus()->value,
            'failed_login_count' => $authenticationAccount->LoginRestriction()->failedLoginCount(),
            'next_login_allowed_at' => $authenticationAccount->LoginRestriction()->nextLoginAllowedAt(),
            'login_restriction_status' => $authenticationAccount->LoginRestriction()->loginRestrictionStatus(),
            'unsubscribe' => $authenticationAccount->unsubscribeStatus()->value,
        ];
    }
}