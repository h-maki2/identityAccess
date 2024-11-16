<?php
declare(strict_types=1);

use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OneTimeTokenTest extends TestCase
{
    public function test_26文字の一時トークンを生成できる()
    {
        // given

        // when
        $OneTimeToken = OneTimeToken::create();

        // then
        $this->assertEquals(26, strlen($OneTimeToken->value));
    }

    #[DataProvider('invalidTokenProvider')]
    public function test_26文字ではない一時トークンを再構築すると例外が発生する($invalidToken)
    {
        // given

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $OneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('+1 minutes'));
        OneTimeToken::reconstruct($invalidToken, $OneTimeTokenExpiration);
    }

    public static function invalidTokenProvider(): array
    {
        return [
            ['c0f1bb19ac3e00100e43efac6'], // 25文字
            ['c0f1bb19ac3e00100e43efac6e1'], // 27文字
            ['']
        ];
    }
}