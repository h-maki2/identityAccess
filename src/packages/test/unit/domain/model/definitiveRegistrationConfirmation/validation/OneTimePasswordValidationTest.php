<?php
declare(strict_types=1);

namespace packages\domain\model\definitiveRegistrationConfirmation\validation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OneTimePasswordValidationTest extends TestCase
{
    #[DataProvider('invalidOneTimePasswordProvider')]
    public function test_6桁の数字ではない場合はfalseを返す($invalidOneTimePassword)
    {
        // when
        $result = OneTimePasswordValidation::validate($invalidOneTimePassword);

        // then
        $this->assertFalse($result);
    }

    public function test_有効なワンタイムパスワードの場合はtrueを返す()
    {
        // when
        $result = OneTimePasswordValidation::validate('123456');

        // then
        $this->assertTrue($result);
    }

    public static function invalidOneTimePasswordProvider(): array
    {
        return [
            ['12345'],
            ['1234567'],
            ['abcdef']
        ];
    }
}