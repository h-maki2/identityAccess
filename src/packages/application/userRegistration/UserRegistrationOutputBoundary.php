<?php

namespace packages\application\userRegistration;

interface UserRegistrationOutputBoundary
{
    public function present(UserRegistrationResult $result): void;
}