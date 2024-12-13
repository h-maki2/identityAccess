<?php

namespace packages\application\userProfile\fetch;

interface FetchUserProfileInputBoundary
{
    public function handle(string $scope): FetchUserProfileResult;
}