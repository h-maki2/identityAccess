<?php

namespace packages\adapter\persistence\inMemory;

use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\userProfile\LoginRestriction;
use packages\domain\model\userProfile\FailedLoginCount;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\NextLoginAt;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserPassword;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\VerificationStatus;
use Ramsey\Uuid\Uuid;

class InMemoryUserProfileRepository implements IUserProfileRepository
{
    private array $userProfileList;

    public function findByEmail(UserEmail $email): ?UserProfile
    {
        foreach ($this->userProfileList as $userProfileModel) {
            if ($userProfileModel->email === $email->value) {
                return $this->toUserProfile($userProfileModel);
            }
        }

        return null;
    }

    public function findById(UserId $id): ?UserProfile
    {
        $userProfileModel = $this->userProfileList[$id->value] ?? null;
        if ($userProfileModel === null) {
            return null;
        }

        return $this->toUserProfile($userProfileModel);
    }

    public function save(UserProfile $userProfile): void
    {
        $this->userProfileList[$userProfile->id()->value] = $this->toUserProfileModel($userProfile);
    }

    public function delete(UserId $id): void
    {
        if (!isset($this->userProfileList[$id()->value])) {
            return;
        }

        unset($this->userProfileList[$id()->value]);
    }

    public function nextUserId(): UserId
    {
        return new UserId(new IdentifierFromUUIDver7(), Uuid::uuid7());
    }

    private function toUserProfile(object $userProfileModel): UserProfile
    {
        return UserProfile::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $userProfileModel->user_id),
            new UserEmail($userProfileModel->email),
            UserName::create($userProfileModel->username),
            UserPassword::reconstruct($userProfileModel->password),
            VerificationStatus::from($userProfileModel->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($userProfileModel->failed_login_count),
                $userProfileModel->next_login_at !== null ? NextLoginAt::reconstruct(new DateTimeImmutable($userProfileModel->next_login_at)) : null
            )
        );
    }

    private function toUserProfileModel(UserProfile $userProfile): object
    {
        return (object) [
            'user_id' => $userProfile->id()->value,
            'username' => $userProfile->name()->value,
            'email' => $userProfile->email()->value,
            'password' => $userProfile->password()->hashedValue,
            'verification_status' => $userProfile->verificationStatus()->value,
            'failed_login_count' => $userProfile->LoginRestriction()->failedLoginCount(),
            'next_login_at' => $userProfile->LoginRestriction()->nextLoginAt()
        ];
    }
}