<?php

namespace packages\domain\model\authenticationInformation;

use packages\domain\model\authenticationInformation\UserId;

interface SessionAuthentication
{
    /**
     * ログイン済み状態にする
     */
    public function markAsLoggedIn(UserId $userId): void;

    /**
     * ログインしているユーザーのIDを取得する
     */
    public function getUserId(): ?UserId;

    public function logout(): void;
}