<?php

namespace packages\domain\model\userProfile;

use packages\domain\model\authenticationAccount\UserId;

interface IUserProfileRepository
{
    public function findByUserName(UserName $userName): ?UserProfile;

    public function findByUserId(UserId $userId): ?UserProfile;

    public function findByProfileId(UserProfileId $userProfileId): ?UserProfile;

    public function save(UserProfile $userProfile): void;

    public function delete(UserProfileId $id): void;

    public function nextUserProfileId(): UserProfileId;
}