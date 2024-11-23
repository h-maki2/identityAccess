<?php

namespace packages\domain\model\oauth\authToken;

interface IRefreshTokenDeactivationService
{
    /**
     * リフレッシュトークンを無効化する
     */
    public function deactivate(AccessToken $accessToken): void;
}