<?php
declare(strict_types=1);

use packages\domain\model\AuthenticationInformation\UserEmail;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UserEmailTest extends TestCase
{
    public function test_メールアドレスが空の場合に例外が発生する()
    {
        // given
        $emailString = '';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('メールアドレスが空です。');
        new UserEmail($emailString);
    }

    #[DataProvider('invalidEmailProvider')]
    public function test_メールアドレスの形式が無効の場合に例外が発生する(string $emailString)
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('無効なメールアドレスです。');
        new UserEmail($emailString);
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['　'],
            [' '],
            ['aaaa@.com'],
            ['@example.com'],
            ['　@example.com'],
            [' @example.com'],
            ['aaaaaa'],
            ['aaaa@aaa.c']
        ];
    }
}