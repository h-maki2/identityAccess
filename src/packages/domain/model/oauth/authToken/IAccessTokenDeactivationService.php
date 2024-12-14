<?php

namespace packages\domain\model\oauth\authToken;

interface IAccessTokenDeactivationService
{
    /**
     * アクセストークンを無効化する
     */
    public function deactivate(AccessToken $accessToken): void;
}