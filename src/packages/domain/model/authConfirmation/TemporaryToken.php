<?php

namespace packages\domain\model\authConfirmation;

use DateTime;
use InvalidArgumentException;
use packages\domain\model\common\identifier\Identifier;
use packages\domain\service\common\identifier\FetchElapsedTimeFromIdentifier;

class TemporaryToken
{
    readonly string $value;

    private const EFFECTIVE_TIME = 24;

    public function __construct(Identifier $identifier, string $value)
    {
        if ($identifier->isValidLength($value)) {
            throw new InvalidArgumentException('適切な文字列の長さではありません。');
        }

        if (!$identifier->isValidFormat($value)) {
            throw new InvalidArgumentException('適切な形式になっていません。');
        }

        $this->value = $value;
    }

    /**
     * 有効なトークンかどうかを判定する
     */
    public function isValid(FetchElapsedTimeFromIdentifier $fetchElapsedTimeFromIdentifier, DateTime $today): bool
    {
        return $this->elapsedTime($fetchElapsedTimeFromIdentifier, $today) <= self::EFFECTIVE_TIME;
    }

    /**
     * tokenが生成されてからの経過時間を取得する
     */
    private function elapsedTime(FetchElapsedTimeFromIdentifier $fetchElapsedTimeFromIdentifier, DateTime $today): int
    {
        return $fetchElapsedTimeFromIdentifier->handle($this->value, $today);
    }
}