<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use DateTimeImmutable;
use InvalidArgumentException;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\service\registration\definitiveRegistration\OneTimeTokenExistsService;

class DefinitiveRegistrationConfirmation
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

    public static function create(
        UserId $userId, 
        OneTimeToken $oneTimeToken,
        OneTimeTokenExistsService $oneTimeTokenExistsService
    ): self
    {
        if ($oneTimeTokenExistsService->isExists($oneTimeToken->tokenValue())) {
            throw new InvalidArgumentException('OneTimeToken is already exists.');
        }

        return new self(
            $userId,
            $oneTimeToken,
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

    public function oneTimeToken(): OneTimeToken
    {
        return $this->oneTimeToken;
    }

    public function oneTimePassword(): OneTimePassword
    {
        return $this->oneTimePassword;
    }

    /**
     * 本登録確認の再取得を行う
     * ワンタイムトークンとワンタイムパスワードを再生成する
     */
    public function reObtain(): void
    {
        $this->oneTimeToken = OneTimeToken::create();
        $this->oneTimePassword = OneTimePassword::create();
    }

    /**
     * 本登録確認済みに更新できるかどうかを判定する
     */
    public function canUpdatConfirmed(OneTimePassword $otherOneTimePassword, DateTimeImmutable $currentDateTime): bool
    {
        return $this->oneTimePassword->equals($otherOneTimePassword) && !$this->oneTimeToken->isExpired($currentDateTime);
    }
}