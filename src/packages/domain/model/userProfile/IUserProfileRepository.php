<?php

namespace packages\domain\model\userProfile;

interface IUserProfileRepository
{
    public function findByEmail(UserEmail $email): ?UserProfile;

    public function findById(UserId $id): ?UserProfile;

    public function save(UserProfile $userProfile): void;

    public function delete(UserId $id): void;

    public function nextUserId(): UserId;
}