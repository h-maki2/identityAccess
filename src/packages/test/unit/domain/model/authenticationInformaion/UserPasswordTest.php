<?php
declare(strict_types=1);

use packages\domain\model\AuthenticationInformation\UserPassword;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UserPasswordTest extends TestCase
{

    #[DataProvider('validPasswordProvider')]
    public function test_適切なパスワードの形式を入力した場合にインスタンスを生成できる(string $password)
    {
        // when
        $userPassword = UserPassword::create($password);

        // then
        $this->assertInstanceOf(UserPassword::class, $userPassword);
    }

    #[DataProvider('invalidPasswordProvider')]
    public function test_不適切な形式のパスワードを入力した場合に例外が発生する(string $password)
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('適切なパスワードではありません。');
        UserPassword::create($password);
    }

    public function test_ハッシュ化していないパスワードを入力してインスタンスを生成した場合に例外が発生する()
    {
        // when・then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('パスワードがハッシュ化されてません。');
        $password = '123456abcdef_?+-';
        UserPassword::reconstruct($password);
    }

    public function test_入力されたパスワードが正しいかどうかを確認できる()
    {
        // given
        $password = '123456abcdeF_?+-あいうえ';
        $userPassword = UserPassword::create($password);

        // when
        $inputedPassword = '123456abcdeF_?+-あいうえ';
        $result = $userPassword->equals($inputedPassword);

        // then
        $this->assertTrue($result);
    }

    public function test_入力されたパスワードが正しくない場合を確認できる()
    {
        // given
        $password = '123456abcdDf_?+-';
        $userPassword = UserPassword::create($password);

        // when
        $wrongInputedPassword = '123456Abcdef_?+-)';
        $result = $userPassword->equals($wrongInputedPassword);

        // then
        $this->assertFalse($result);
    }

    public static function validPasswordProvider(): array
    {
        return [
            ['123456hassjrHusausj_'],
            ['H?siejje_84jj4dha'],
            ['__=\3-48Ddjcjalwowあいうえ'],
            ['c<>dlldmvmEe123?_'],
            ['(djnnej%^dSS3455cna#あ']
        ];
    }

    public static function invalidPasswordProvider(): array
    {
        return [
            ['1223ssccdikww?_'], // 大文字を含んでいない
            ['acsddwDSFD_'], // 数字を含んでいない
            ['acssefeUHS8776'], // 特殊文字列を含んでいない
            ['aA8_'], // 8文字未満
            ['ASDDXUUSN5639829_'], // 小文字を含んでない
        ];
    }
}