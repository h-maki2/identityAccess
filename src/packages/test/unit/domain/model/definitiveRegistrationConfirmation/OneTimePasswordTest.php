<?php
declare(strict_types=1);

use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OneTimePasswordTest extends TestCase
{
    public function test_6桁のランダムな整数が生成される()
    {
        // given

        // when
        $oneTimePassword = OneTimePassword::create();

        // then
        $this->assertEquals(6, strlen((string)$oneTimePassword->value));
    }

    #[DataProvider('invalidOneTimePasswordProvider')]
    public function test_6桁以外の整数でインスタンスを生成した場合は例外が発生する($invalidOneTimePassword)
    {
        // given

        // when・then
        $this->expectException(InvalidArgumentException::class);
        OneTimePassword::reconstruct($invalidOneTimePassword);
    }

    public function test_ワンタイムパスワードが等しいかどうかを判定できる()
    {
        // given
        $oneTimePasswordString = '123456';
        $oneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);
        $otherOneTimePassword = OneTimePassword::reconstruct($oneTimePasswordString);

        // when
        $result = $oneTimePassword->equals($otherOneTimePassword);

        // then
        $this->assertTrue($result);
    }

    public static function invalidOneTimePasswordProvider(): array
    {
        return [
            ['12345'],
            ['1234567'],
            ['1']
        ];
    }
}