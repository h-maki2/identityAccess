<?php

namespace packages\adapter\persistence\eloquent;

use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\UserProfileId;
use App\Models\UserProfile as EloquentUserProfile;
use RuntimeException;
use Ramsey\Uuid\Uuid;

class EloquentUserProfileRepository implements IUserProfileRepository
{

    public function findByUserName(UserName $userName): ?UserProfile
    {
        $result = EloquentUserProfile::where('user_name', $userName->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toUserProfile($result);
    }

    public function findByUserId(UserId $userId): ?UserProfile
    {
        $result = EloquentUserProfile::where('user_id', $userId->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toUserProfile($result);
    }

    public function findByProfileId(UserProfileId $userProfileId): ?UserProfile
    {
        $result = $this->eloquentUserProfileFrom($userProfileId);

        if ($result === null) {
            return null;
        }

        return $this->toUserProfile($result);
    }

    public function save(UserProfile $userProfile): void
    {
        $eloquentUserProfile = $this->toEloquentUserProfile($userProfile);
        $eloquentUserProfile->save();
    }

    public function delete(UserProfile $userProfile): void
    {
        $eloquentUserProfile = $this->eloquentUserProfileFrom($userProfile->profileId());

        if ($eloquentUserProfile === null) {
            throw new RuntimeException('ユーザープロフィールが存在しません。user_profile_id: ' . $userProfile->profileId()->value);
        }

        $eloquentUserProfile->delete();
    }

    public function nextUserProfileId(): UserProfileId
    {
        return new UserProfileId(Uuid::uuid7()->toString());
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

    private function toEloquentUserProfile(UserProfile $userProfile): EloquentUserProfile
    {
        $eloquentUserProfile = $this->eloquentUserProfileFrom($userProfile->profileId());

        if ($eloquentUserProfile === null) {
            $eloquentUserProfile = new EloquentUserProfile();
            $eloquentUserProfile->user_profile_id = $userProfile->profileId()->value;
        }

        $eloquentUserProfile->user_id = $userProfile->userId()->value;
        $eloquentUserProfile->user_name = $userProfile->name()->value;
        $eloquentUserProfile->self_introduction_text = $userProfile->selfIntroductionText()->value;

        return $eloquentUserProfile;
    }

    private function eloquentUserProfileFrom(UserProfileId $userProfileId): ?EloquentUserProfile
    {
        return EloquentUserProfile::find($userProfileId->value);
    }
}