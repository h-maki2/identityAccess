<?php

namespace packages\test\helpers\userProfile;

use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\UserProfileId;

class UserProfileTestDataCreator
{
    private IUserProfileRepository $userProfileRepository;
    private IAuthenticationInformationRepository $authenticationInformationRepository;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        IAuthenticationInformationRepository $authenticationInformationRepository
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->authenticationInformationRepository = $authenticationInformationRepository;
    }

    public function create(
        UserId $userId,
        ?UserProfileId $profileId = null,
        ?UserName $userName = null,
        ?SelfIntroductionText $selfIntroductionText = null
    ): UserProfile 
    {
        $authenticationInformation = $this->authenticationInformationRepository->findById($userId);
        if ($authenticationInformation === null) {
            throw new \RuntimeException('認証情報テーブルに事前にデータを登録してください。');
        }

        $userProfile = TestUserProfileFactory::create($userId, $profileId, $userName, $selfIntroductionText);
        $this->userProfileRepository->save($userProfile);

        return $userProfile;
    }
}