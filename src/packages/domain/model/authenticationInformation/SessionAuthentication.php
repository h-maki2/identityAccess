<?php

namespace packages\domain\model\AuthenticationInformation;

use packages\domain\model\AuthenticationInformation\UserId;

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