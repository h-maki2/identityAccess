<?php

namespace packages\adapter\persistence\inMemory;

use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserProfile;

class InMemoryUserProfileRepository implements IUserProfileRepository
{
    private array $userProfileList;

    public function save(UserProfile $userProfile): void
    {
        $userProfileList[$userProfile->id()->value] = (object) [
            
        ];
    }
}