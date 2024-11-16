<?php

namespace packages\domain\model\authConfirmation;

use DateTimeImmutable;
use packages\domain\model\authenticationInformaion\UserId;

class AuthConfirmation
{
    readonly UserId $userId;
    private OneTimeToken $oneTimeToken;
    private OneTimePassword $oneTimePassword;

    private function __construct(
        UserId $userId, 
        OneTimeToken $oneTimeToken, 
        OneTimePassword $oneTimePassword
    )
    {
        $this->userId = $userId;
        $this->oneTimeToken = $oneTimeToken;
        $this->oneTimePassword = $oneTimePassword;
    }

    public static function create(UserId $userId): self
    {
        return new self(
            $userId,
            OneTimeToken::create(),
            OneTimePassword::create()
        );
    }

    public static function reconstruct(
        UserId $userId, 
        OneTimeToken $oneTimeToken, 
        OneTimePassword $oneTimePassword
    ): self
    {
        return new self($userId, $oneTimeToken, $oneTimePassword);
    }

    public function OneTimeToken(): OneTimeToken
    {
        return $this->oneTimeToken;
    }

    public function OneTimePassword(): OneTimePassword
    {
        return $this->oneTimePassword;
    }

    /**
     * 認証確認の再取得を行う
     * ワンタイムトークンとワンタイムパスワードを再生成する
     */
    public function ReObtain(): void
    {
        $this->oneTimeToken = OneTimeToken::create();
        $this->oneTimePassword = OneTimePassword::create();
    }

    /**
     * 有効期限切れかどうかを判定
     */
    public function isExpired(DateTimeImmutable $currentDateTime): bool
    {
        return $this->oneTimeToken->isExpired($currentDateTime);
    }
}