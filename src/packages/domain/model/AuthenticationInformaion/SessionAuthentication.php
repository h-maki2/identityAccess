<?php

namespace packages\domain\model\authenticationInformaion;

use packages\domain\model\authenticationInformaion\UserId;

abstract class SessionAuthentication
{
    /**
     * ログイン済み状態にする
     */
    abstract public function markAsLoggedIn(UserId $userId): void;

    /**
     * ログインしているユーザーのIDを取得する
     */
    abstract public function getUserId(): ?UserId;
}