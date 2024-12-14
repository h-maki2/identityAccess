<?php
declare(strict_types=1);

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

    public function test_ユーザー名が51文字以上の場合に例外が発生する()
    {
        // given
        $userNameString = str_repeat('a', 51);

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

    public function test_ユーザー名が有効な場合にインスタンスが生成される()
    {
        // given
        // 1文字以上50文字以下の文字列
        $userNameString = str_repeat('a', 50);

        // when
        $userName = new UserName($userNameString);

        // then
        $this->assertEquals($userNameString, $userName->value);
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