<?php

namespace packages\application\userProfile\register;

interface RegisterUserProfileOutputBoundary
{
    public function formatForResponse(RegisterUserProfileResult $result): void;
    public function response(): mixed;
}