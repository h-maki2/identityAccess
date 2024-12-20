<?php

namespace packages\adapter\persistence\inMemory;

use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\UserProfileId;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class InMemoryUserProfileRepository implements IUserProfileRepository
{
    private array $userProfiles = [];

    public function findByUserName(UserName $userName): ?UserProfile
    {
        foreach ($this->userProfiles as $userProfile) {
            if ($userProfile->user_name === $userName->value) {
                return $this->toUserProfile($userProfile);
            }
        }

        return null;
    }

    public function findByUserId(UserId $userId): ?UserProfile
    {
        foreach ($this->userProfiles as $userProfile) {
            if ($userProfile->user_id === $userId->value) {
                return $this->toUserProfile($userProfile);
            }
        }

        return null;
    }

    public function findByProfileId(UserProfileId $userProfileId): ?UserProfile
    {
        $userProfile = $this->userProfiles[$userProfileId->value] ?? null;

        if ($userProfile === null) {
            return null;
        }

        return $this->toUserProfile($userProfile);
    }

    public function save(UserProfile $userProfile): void
    {
        $this->userProfiles[$userProfile->profileId()->value] = (object) [
            'user_profile_id' => $userProfile->profileId()->value,
            'user_id' => $userProfile->userId()->value,
            'user_name' => $userProfile->name()->value,
            'self_introduction_text' => $userProfile->selfIntroductionText()->value,
        ];
    }

    public function delete(UserProfile $userProfile): void
    {
        $userProfile = $this->userProfiles[$userProfile->profileId()->value] ?? null;
        if ($userProfile === null) {
            throw new RuntimeException('削除対象の認証アカウントが存在しません。');
        }
        unset($this->userProfiles[$userProfile->profileId()->value]);
    }

    public function nextUserProfileId(): UserProfileId
    {
        return new UserProfileId(Uuid::uuid7());
    }

    private function toUserProfile(object $record): UserProfile
    {
        return UserProfile::reconstruct(
            new UserId($record->user_id),
            new UserProfileId($record->user_profile_id),
            new UserName($record->user_name),
            new SelfIntroductionText($record->self_introduction_text)
        );
    }
}