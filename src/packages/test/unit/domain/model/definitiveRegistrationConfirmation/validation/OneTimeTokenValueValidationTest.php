<?php
declare(strict_types=1);

use packages\domain\model\definitiveRegistrationConfirmation\validation\OneTimeTokenValueValidation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OneTimeTokenValueValidationTest extends TestCase
{
    #[DataProvider('invalidOneTimeTokenValue')]
    public function test_ワンタイムトークンが26文字ではない場合はfalseを返す($invalidOneTimeToken)
    {
        // when
        $result = OneTimeTokenValueValidation::validate($invalidOneTimeToken);

        // then
        $this->assertFalse($result);
    }

    public function test_26文字の有効なワンタイムトークンの場合はtrueを返す()
    {
        // when
        $result = OneTimeTokenValueValidation::validate('abcdefghijABCDEFGHIJ123456');

        // then
        $this->assertTrue($result);
    }

    public static function invalidOneTimeTokenValue(): array
    {
        return [
            ['abcdefghijklmnopqrstuvwxyz123'],
            ['abcdefghijABCDEFGHIJ12345678901'],
            ['　　'],
            ['  '],
            ['']
        ];
    }
}