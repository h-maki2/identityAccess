<?php

namespace packages\domain\service\userRegistration;

interface UserRegistrationOutputBoundary
{
    public function formatForResponse(UserRegistrationResult $result): void;
    public function response();
}