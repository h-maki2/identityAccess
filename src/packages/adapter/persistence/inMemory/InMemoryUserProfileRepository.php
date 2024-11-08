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
use packages\domain\model\authenticationInformaion\UserName;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use Ramsey\Uuid\Uuid;

class InMemoryAuthenticationInformaionRepository implements IAuthenticationInformaionRepository
{
    private array $authenticationInformaionList;

    public function findByEmail(UserEmail $email): ?AuthenticationInformaion
    {
        foreach ($this->AuthenticationInformaionList as $authenticationInformaionModel) {
            if ($authenticationInformaionModel->email === $email->value) {
                return $this->toAuthenticationInformaion($authenticationInformaionModel);
            }
        }

        return null;
    }

    public function findById(UserId $id): ?AuthenticationInformaion
    {
        $authenticationInformaionModel = $this->AuthenticationInformaionList[$id->value] ?? null;
        if ($authenticationInformaionModel === null) {
            return null;
        }

        return $this->toAuthenticationInformaion($authenticationInformaionModel);
    }

    public function save(AuthenticationInformaion $authenticationInformaion): void
    {
        $this->AuthenticationInformaionList[$authenticationInformaion->id()->value] = $this->toAuthenticationInformaionModel($authenticationInformaion);
    }

    public function delete(UserId $id): void
    {
        if (!isset($this->AuthenticationInformaionList[$id()->value])) {
            return;
        }

        unset($this->AuthenticationInformaionList[$id()->value]);
    }

    public function nextUserId(): UserId
    {
        return new UserId(new IdentifierFromUUIDver7(), Uuid::uuid7());
    }

    private function toAuthenticationInformaion(object $authenticationInformaionModel): AuthenticationInformaion
    {
        return AuthenticationInformaion::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $authenticationInformaionModel->user_id),
            new UserEmail($authenticationInformaionModel->email),
            UserName::create($authenticationInformaionModel->username),
            UserPassword::reconstruct($authenticationInformaionModel->password),
            VerificationStatus::from($authenticationInformaionModel->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($authenticationInformaionModel->failed_login_count),
                $authenticationInformaionModel->next_login_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($authenticationInformaionModel->next_login_at)) : null
            )
        );
    }

    private function toAuthenticationInformaionModel(AuthenticationInformaion $authenticationInformaion): object
    {
        return (object) [
            'user_id' => $authenticationInformaion->id()->value,
            'username' => $authenticationInformaion->name()->value,
            'email' => $authenticationInformaion->email()->value,
            'password' => $authenticationInformaion->password()->hashedValue,
            'verification_status' => $authenticationInformaion->verificationStatus()->value,
            'failed_login_count' => $authenticationInformaion->LoginRestriction()->failedLoginCount(),
            'next_login_at' => $authenticationInformaion->LoginRestriction()->NextLoginAllowedAt()
        ];
    }
}