<?php

namespace packages\domain\service\authenticationAccount;

use packages\domain\model\authenticationAccount\UserId;

interface AuthenticationService
{
    /**
     * ログイン済み状態にする
     */
    public function markAsLoggedIn(UserId $userId): void;

    /**
     * ログインしているユーザーのIDを取得する
     */
    public function loggedInUserId(): ?UserId;

    public function logout(): void;
}