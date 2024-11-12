<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\LoginRestrictionStatus;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use Ramsey\Uuid\Uuid;

class InMemoryAuthenticationInformaionRepository implements IAuthenticationInformaionRepository
{
    private array $authenticationInformaionList = [];

    public function findByEmail(UserEmail $email): ?AuthenticationInformaion
    {
        foreach ($this->authenticationInformaionList as $authenticationInformaionObj) {
            if ($authenticationInformaionObj->email === $email->value) {
                return $this->toAuthenticationInformaion($authenticationInformaionObj);
            }
        }

        return null;
    }

    public function findById(UserId $id): ?AuthenticationInformaion
    {
        $authenticationInformaionObj = $this->authenticationInformaionList[$id->value] ?? null;
        if ($authenticationInformaionObj === null) {
            return null;
        }

        return $this->toAuthenticationInformaion($authenticationInformaionObj);
    }

    public function save(AuthenticationInformaion $authenticationInformaion): void
    {
        $this->authenticationInformaionList[$authenticationInformaion->id()->value] = $this->toAuthenticationInformaionModel($authenticationInformaion);
    }

    public function delete(UserId $id): void
    {
        if (!isset($this->authenticationInformaionList[$id()->value])) {
            return;
        }

        unset($this->authenticationInformaionList[$id()->value]);
    }

    public function nextUserId(): UserId
    {
        return new UserId(new IdentifierFromUUIDver7(), Uuid::uuid7());
    }

    private function toAuthenticationInformaion(object $authenticationInformaionObj): AuthenticationInformaion
    {
        return AuthenticationInformaion::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $authenticationInformaionObj->user_id),
            new UserEmail($authenticationInformaionObj->email),
            UserPassword::reconstruct($authenticationInformaionObj->password),
            VerificationStatus::from($authenticationInformaionObj->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($authenticationInformaionObj->failed_login_count),
                LoginRestrictionStatus::from($authenticationInformaionObj->login_restriction_status),
                $authenticationInformaionObj->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($authenticationInformaionObj->next_login_allowed_at)) : null
            )
        );
    }

    private function toAuthenticationInformaionModel(AuthenticationInformaion $authenticationInformaion): object
    {
        return (object) [
            'user_id' => $authenticationInformaion->id()->value,
            'email' => $authenticationInformaion->email()->value,
            'password' => $authenticationInformaion->password()->hashedValue,
            'verification_status' => $authenticationInformaion->verificationStatus()->value,
            'failed_login_count' => $authenticationInformaion->LoginRestriction()->failedLoginCount(),
            'next_login_allowed_at' => $authenticationInformaion->LoginRestriction()->nextLoginAllowedAt(),
            'login_restriction_status' => $authenticationInformaion->LoginRestriction()->loginRestrictionStatus()
        ];
    }
}