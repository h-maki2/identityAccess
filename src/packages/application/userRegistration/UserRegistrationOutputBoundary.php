<?php

namespace packages\domain\service\userRegistration;

abstract class UserRegistrationOutputBoundary
{
    abstract public function formatForResponse(UserRegistrationResult $result): void;
    abstract public function response();
}