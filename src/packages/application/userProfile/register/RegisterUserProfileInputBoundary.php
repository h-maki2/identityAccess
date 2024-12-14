<?php

namespace packages\application\userProfile\register;

interface RegisterUserProfileInputBoundary
{
    public function register(
        string $userNameString, 
        string $selfIntroductionTextString,
        string $scope
    ): RegisterUserProfileResult;
}