<?php

namespace packages\domain\model\authConfirmation;

use DateTime;
use packages\domain\model\userProfile\UserId;
use packages\domain\service\common\token\FetchElapsedTimeFromToken;

class AuthConfirmation
{
    readonly UserId $userId;
    readonly TemporaryToken $token;

    public function __construct(UserId $userId, TemporaryToken $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    /**
     * 有効な認証確認情報かどうかを判定する
     */
    public function isValid(FetchElapsedTimeFromToken $fetchElapsedTimeFromToken, DateTime $today): bool
    {
        return $this->token->isValid($fetchElapsedTimeFromToken, $today);
    }
}