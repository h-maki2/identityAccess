<?php

namespace packages\domain\model\userProfile;

use DomainException;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\service\userProfile\UserProfileService;

class UserProfile
{
    private UserId $userId;
    private UserProfileId $userProfileId;
    private UserName $userName;
    private SelfIntroductionText $selfIntroductionText;

    private function __construct(
        UserId $userId, 
        UserProfileId $userProfileId, 
        UserName $userName, 
        SelfIntroductionText $selfIntroductionText
    )
    {
        $this->userId = $userId;
        $this->userProfileId = $userProfileId;
        $this->userName = $userName;
        $this->selfIntroductionText = $selfIntroductionText;
    }

    public static function create(
        UserId $userId, 
        UserProfileId $userProfileId, 
        UserName $userName, 
        SelfIntroductionText $selfIntroductionText,
        UserProfileService $userProfileService
    ): self
    {
        if ($userProfileService->alreadyExistsUserName($userName)) {
            throw new DomainException('既に登録されているユーザー名です。userName: ' . $userName->value);
        }

        return new self($userId, $userProfileId, $userName, $selfIntroductionText);
    }

    public static function reconstruct(
        UserId $userId, 
        UserProfileId $userProfileId, 
        UserName $userName, 
        SelfIntroductionText $selfIntroductionText
    ): self
    {
        return new self($userId, $userProfileId, $userName, $selfIntroductionText);
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function profileId(): UserProfileId
    {
        return $this->userProfileId;
    }

    public function name(): UserName
    {
        return $this->userName;
    }

    public function selfIntroductionText(): SelfIntroductionText
    {
        return $this->selfIntroductionText;
    }

    /**
     * ユーザー名を変更する
     */
    public function changeName(UserName $userName, UserProfileService $userProfileService): void
    {
        if ($userProfileService->alreadyExistsUserName($userName)) {
            throw new DomainException('既に登録されているユーザー名です。userName: ' . $userName->value);
        }

        $this->userName = $userName;
    }

    /**
     * 自己紹介文を変更する
     */
    public function changeSelfIntroductionText(SelfIntroductionText $selfIntroductionText): void
    {
        $this->selfIntroductionText = $selfIntroductionText;
    }
}