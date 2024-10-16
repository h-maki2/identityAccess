<?php
declare(strict_types=1);

use packages\domain\model\userProfile\UserEmail;
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

    public function test_メールアドレスのローカル部を取得できる()
    {
        // given
        $emailString = 'test@example.com';
        $email = new UserEmail($emailString);

        // when
        $emailLocalPart = $email->localPart();

        // then
        $this->assertEquals('test', $emailLocalPart);
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
            ['aaaaaa']
        ];
    }
}