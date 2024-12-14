<?php

namespace packages\application\userProfile\fetch;

class FetchUserProfileResult
{
    readonly string $userProfileId;
    readonly string $userName;
    readonly string $selfIntroductionText;
    readonly bool $isExistsUserProfile;

    private function __construct(
        string $userProfileId, 
        string $userName, 
        string $selfIntroductionText,
        bool $isExistsUserProfile
    )
    {
        $this->userProfileId = $userProfileId;
        $this->userName = $userName;
        $this->selfIntroductionText = $selfIntroductionText;
        $this->isExistsUserProfile = $isExistsUserProfile;
    }

    public static function createWhenNotFound(): FetchUserProfileResult
    {
        return new FetchUserProfileResult('', '', '', false);
    }

    public static function createWhenFound(
        string $userProfileId, 
        string $userName, 
        string $selfIntroductionText
    ): FetchUserProfileResult
    {
        return new FetchUserProfileResult($userProfileId, $userName, $selfIntroductionText, true);
    }
}