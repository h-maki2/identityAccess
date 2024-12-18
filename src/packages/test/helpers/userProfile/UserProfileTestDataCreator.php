<?php

namespace packages\test\helpers\userProfile;

use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\UserProfileId;

class UserProfileTestDataCreator
{
    private IUserProfileRepository $userProfileRepository;
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        IAuthenticationAccountRepository $authenticationAccountRepository
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    public function create(
        UserId $userId,
        ?UserProfileId $profileId = null,
        ?UserName $userName = null,
        ?SelfIntroductionText $selfIntroductionText = null
    ): UserProfile 
    {
        $authenticationAccount = $this->authenticationAccountRepository->findById($userId);
        if ($authenticationAccount === null) {
            throw new \RuntimeException('認証アカウントテーブルに事前にデータを登録してください。');
        }

        $userProfile = TestUserProfileFactory::create($userId, $profileId, $userName, $selfIntroductionText);
        $this->userProfileRepository->save($userProfile);

        return $userProfile;
    }
}