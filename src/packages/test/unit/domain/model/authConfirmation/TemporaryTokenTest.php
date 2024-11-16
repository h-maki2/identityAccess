<?php
declare(strict_types=1);

use packages\domain\model\authConfirmation\TemporaryToken;
use packages\domain\model\authConfirmation\TemporaryTokenExpiration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TemporaryTokenTest extends TestCase
{
    public function test_26文字の一時トークンを生成できる()
    {
        // given

        // when
        $temporaryToken = TemporaryToken::create();

        // then
        $this->assertEquals(26, strlen($temporaryToken->value));
    }

    #[DataProvider('invalidTokenProvider')]
    public function test_26文字ではない一時トークンを再構築すると例外が発生する($invalidToken)
    {
        // given

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $temporaryTokenExpiration = TemporaryTokenExpiration::reconstruct(new DateTimeImmutable('+1 minutes'));
        TemporaryToken::reconstruct($invalidToken, $temporaryTokenExpiration);
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