<?php

namespace packages\application\changePassword\change;

interface ChangePasswordApplicationInputBoundary
{
    public function changePassword(
        string $scopeString,
        string $passwordString,
        string $clientId,
        string $redirectUrl
    ): ChangePasswordResult;
}