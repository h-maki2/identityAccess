<?php
declare(strict_types=1);

use packages\domain\model\userProfile\UserPassword;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UserPasswordTest extends TestCase
{

    #[DataProvider('passwordListProvider')]
    public function test_インスタンスを生成できる(string $password)
    {
        // when
        $userPassword = UserPassword::create($password);

        // then
        $this->assertInstanceOf(UserPassword::class, $userPassword);
    }

    public function test_ハッシュ化していないパスワードを入力してインスタンスを生成した場合に例外が発生する()
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('パスワードがハッシュ化されてません。');
        $password = '123456abcdef_?+-';
        UserPassword::reconstruct($password);
    }

    public function test_パスワードが空の場合に例外が発生する()
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('パスワードが空です。');
        UserPassword::reconstruct('');
    }

    public function test_入力されたパスワードが正しいかどうかを確認できる()
    {
        // given
        $password = '123456abcdef_?+-';
        $userPassword = UserPassword::create($password);

        // when
        $inputedPassword = '123456abcdef_?+-';
        $result = $userPassword->equals($inputedPassword);

        // then
        $this->assertTrue($result);
    }

    public function test_入力されたパスワードが正しくない場合を確認できる()
    {
        // given
        $password = '123456abcdef_?+-';
        $userPassword = UserPassword::create($password);

        // when
        $wrongInputedPassword = '123456abcdef_?+-)';
        $result = $userPassword->equals($wrongInputedPassword);

        // then
        $this->assertFalse($result);
    }

    public static function passwordListProvider(): array
    {
        return [
            ['123456hassjrnusausj'],
            ['H?siejje_84jj4dha'],
            ['__=\3-48ddjcjalwow'],
            ['c<>dlldmvmee?'],
            ['(djnnej%^djcna#']
        ];
    }
}