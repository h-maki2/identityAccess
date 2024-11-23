<?php

namespace packages\domain\service\userRegistration;

abstract class UserRegistrationOutputBoundary
{
    abstract public function present(UserRegistrationResult $result): void;
    abstract public function response();
}