<?php

namespace packages\application\changePassword;

interface ChangePasswordApplicationInputBoundary
{
    public function changePassword(
        string $scopeString,
        string $passwordString
    ): ChangePasswordResult;
}