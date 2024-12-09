<?php

namespace packages\application\userProfile\register;

use Illuminate\Contracts\Session\Session;
use packages\domain\model\authenticationInformation\AuthenticationService;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\SelfIntroductionText;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\validation\SelfIntroductionTextValidation;
use packages\domain\model\userProfile\validation\UserNameValidation;
use packages\domain\service\userProfile\UserProfileService;
use RuntimeException;

class RegisterUserProfileApplicationService implements RegisterUserProfileInputBoundary
{
    private IUserProfileRepository $userProfileRepository;
    private UserProfileService $userProfileService;
    private AuthenticationService $authService;
    private RegisterUserProfileOutputBoundary $outputBoundary;

    public function __construct(
        IUserProfileRepository $userProfileRepository,
        AuthenticationService $authService,
        RegisterUserProfileOutputBoundary $outputBoundary
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->userProfileService = new UserProfileService($userProfileRepository);
        $this->authService = $authService;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * ユーザー登録を行う
     */
    public function register(string $userNameString, string $selfIntroductionTextString): RegisterUserProfileOutputBoundary
    {
        $userId = $this->authService->loggedInUserId();
        if ($userId === null) {
            throw new RuntimeException('ユーザーがログインしていません');
        }

        $validationHandler = new ValidationHandler();
        $validationHandler->addValidator(new UserNameValidation(
            $this->userProfileRepository,
            $userNameString
        ));
        $validationHandler->addValidator(new SelfIntroductionTextValidation($selfIntroductionTextString));
        if (!$validationHandler->validate()) {
            $this->outputBoundary->formatForResponse(
                RegisterUserProfileResult::createWhenFailure($validationHandler->errorMessages())
            );
            return $this->outputBoundary;
        }

        $userName = New UserName($userNameString);
        $selfIntroductionText = new SelfIntroductionText($selfIntroductionTextString);

        $userProfile = UserProfile::create(
            $userId,
            $this->userProfileRepository->nextUserProfileId(),
            $userName,
            $selfIntroductionText,
            $this->userProfileService
        );
        $this->userProfileRepository->save($userProfile);

        $this->outputBoundary->formatForResponse(RegisterUserProfileResult::createWhenSuccess());
        return $this->outputBoundary;
    }
}