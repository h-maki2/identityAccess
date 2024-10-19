<?php

namespace packages\domain\model\authConfirmation;

use DateTime;
use InvalidArgumentException;
use packages\domain\service\common\token\FetchElapsedTimeFromToken;

class TemporaryToken
{
    readonly string $value;

    private const TOKEN_LENGTH = 36;
    private const EFFECTIVE_TIME = 24;

    public function __construct(string $value)
    {
        if (strlen($value) !== self::TOKEN_LENGTH) {
            throw new InvalidArgumentException('TemporaryTokenは36文字です。');
        }

        if (!$this->isUuidV7($value)) {
            throw new InvalidArgumentException('UUID ver7の形式になっていません。');
        }

        $this->value = $value;
    }

    /**
     * 有効なトークンかどうかを判定する
     */
    public function isValid(FetchElapsedTimeFromToken $fetchElapsedTimeFromToken, DateTime $today): bool
    {
        return $this->elapsedTime($fetchElapsedTimeFromToken, $today) <= self::EFFECTIVE_TIME;
    }

    /**
     * tokenが生成されてからの経過時間を取得する
     */
    private function elapsedTime(FetchElapsedTimeFromToken $fetchElapsedTimeFromToken, DateTime $today): int
    {
        return $fetchElapsedTimeFromToken->handle($this->value, $today);
    }

    private function isUuidV7(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    }
}