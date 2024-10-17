<?php
declare(strict_types=1);

use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserName;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UserNameTest extends TestCase
{
    public function test_ユーザー名が空の場合に例外が発生する()
    {
        // given
        $userNameString = '';

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザー名が無効です。');
        new UserName($userNameString);
    }

    public function test_ユーザー名が21文字以上の場合に例外が発生する()
    {
        // given
        $userNameString = str_repeat('a', 21);

        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザー名が無効です。');
        new UserName($userNameString);
    }

    #[DataProvider('invalidUserNameProvider')]
    public function test_ユーザー名が空白だけの場合に例外が発生する($invalidUserName)
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ユーザー名が空です。');
        new UserName($invalidUserName);
    }

    public function test_ユーザー名の初期値にはメールアドレスのローカル部が設定される()
    {
        // given
        $userEmail = new UserEmail('test@example.com');

        // when
        $userName = UserName::initialization($userEmail);

        // then
        $this->assertEquals('test', $userName->value);
    }

    public function test_メールアドレスのローカル部が21文字以上の場合、20文字以内になるように切り取られたローカル部がユーザー名に設定される()
    {
        // given 21文字以上のローカル部を持つメールアドレス
        $userEmail = new UserEmail('12345678901234567890123456@example.com');

        // when
        $userName = UserName::initialization($userEmail);

        // then
        $this->assertEquals('12345678901234567890', $userName->value);
    }

    public static function invalidUserNameProvider(): array
    {
        return [
            [' '],
            ['  '],
            ['　'],
            ['　　'],
            ['　 '],
            [' 　']
        ];
    }
}