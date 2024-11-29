<?php

namespace packages\domain\service\userProfile;

use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserName;

class UserProfileService
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * 既に登録されているユーザー名かどうかを判定する
     */
    public function alreadyExistsUserName(UserName $userName): bool
    {
        return $this->userProfileRepository->findByUserName($userName) !== null;
    }
}