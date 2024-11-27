<?php

namespace packages\application\userRegistration;

interface UserRegistrationOutputBoundary
{
    public function formatForResponse(UserRegistrationResult $result): void;
    public function response();
}