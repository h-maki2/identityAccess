<?php

namespace packages\domain\service\userRegistration;

interface UserRegistrationOutputBoundary
{
    public function present(UserRegistrationResult $result): void;
}