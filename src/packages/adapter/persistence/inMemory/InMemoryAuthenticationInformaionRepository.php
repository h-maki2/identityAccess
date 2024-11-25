<?php

namespace packages\adapter\persistence\inMemory;

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

class InMemoryAuthenticationInformationRepository implements IAuthenticationInformationRepository
{
    private array $authenticationInformationList = [];

    public function findByEmail(UserEmail $email): ?AuthenticationInformation
    {
        foreach ($this->authenticationInformationList as $authenticationInformationObj) {
            if ($authenticationInformationObj->email === $email->value) {
                return $this->toAuthenticationInformation($authenticationInformationObj);
            }
        }

        return null;
    }

    public function findById(UserId $id): ?AuthenticationInformation
    {
        $authenticationInformationObj = $this->authenticationInformationList[$id->value] ?? null;
        if ($authenticationInformationObj === null) {
            return null;
        }

        return $this->toAuthenticationInformation($authenticationInformationObj);
    }

    public function save(AuthenticationInformation $authenticationInformation): void
    {
        $this->authenticationInformationList[$authenticationInformation->id()->value] = $this->toAuthenticationInformationModel($authenticationInformation);
    }

    public function delete(UserId $id): void
    {
        if (!isset($this->authenticationInformationList[$id()->value])) {
            return;
        }

        unset($this->authenticationInformationList[$id()->value]);
    }

    public function nextUserId(): UserId
    {
        return new UserId(new IdentifierFromUUIDver7(), Uuid::uuid7());
    }

    private function toAuthenticationInformation(object $authenticationInformationObj): AuthenticationInformation
    {
        return AuthenticationInformation::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $authenticationInformationObj->user_id),
            new UserEmail($authenticationInformationObj->email),
            UserPassword::reconstruct($authenticationInformationObj->password),
            VerificationStatus::from($authenticationInformationObj->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($authenticationInformationObj->failed_login_count),
                LoginRestrictionStatus::from($authenticationInformationObj->login_restriction_status),
                $authenticationInformationObj->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($authenticationInformationObj->next_login_allowed_at)) : null
            )
        );
    }

    private function toAuthenticationInformationModel(AuthenticationInformation $authenticationInformation): object
    {
        return (object) [
            'user_id' => $authenticationInformation->id()->value,
            'email' => $authenticationInformation->email()->value,
            'password' => $authenticationInformation->password()->hashedValue,
            'verification_status' => $authenticationInformation->verificationStatus()->value,
            'failed_login_count' => $authenticationInformation->LoginRestriction()->failedLoginCount(),
            'next_login_allowed_at' => $authenticationInformation->LoginRestriction()->nextLoginAllowedAt(),
            'login_restriction_status' => $authenticationInformation->LoginRestriction()->loginRestrictionStatus()
        ];
    }
}