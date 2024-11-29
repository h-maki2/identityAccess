<?php

namespace packages\domain\model\userProfile;

use packages\domain\model\authenticationInformation\UserId;

interface IUserProfileRepository
{
    public function findByUserName(UserName $userName): ?UserProfile;

    public function findByUserId(UserId $userId): ?UserProfile;

    public function save(UserProfile $userProfile): void;

    public function delete(UserProfile $userProfile): void;

    public function nextUserProfileId(): UserProfileId;
}