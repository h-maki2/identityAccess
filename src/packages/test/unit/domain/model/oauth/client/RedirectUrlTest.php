<?php
declare(strict_types=1);

namespace packages\domain\model\oauth\client;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RedirectUrlTest extends TestCase
{
    #[DataProvider('validDataProvider')]
    public function test_入力されたデータがURL形式である場合、インスタンスを生成できる(string $validUrl)
    {
        // given

        // when
        $redirectUrl = new RedirectUrl($validUrl);

        // then
        $this->assertEquals($validUrl, $redirectUrl->value);
    }

    #[DataProvider('inValidDataProvider')]
    public function test_入力されたデータがURL形式でない場合、例外が発生する(string $inValidUrl)
    {
        // when・then
        $this->expectException(\InvalidArgumentException::class);
        new RedirectUrl($inValidUrl);
    }

    public function test_equalsメソッドの引数に入力したURLと同じURLを持っている場合にtrueを返す()
    {
        // given
        $url = 'http://example.com/callback';
        $redirectUrl = new RedirectUrl($url);
        $other = new RedirectUrl($url);

        // when
        $result = $redirectUrl->equals($other);

        // then
        $this->assertTrue($result);
    }

    public function test_equalsメソッドの引数に入力したURLと異なるURLを持っている場合にfalseを返す()
    {
        // given
        $redirectUrl = new RedirectUrl('http://example.com/callback');
        $other = new RedirectUrl('http://example.com/callback/test');

        // when
        $result = $redirectUrl->equals($other);

        // then
        $this->assertFalse($result);
    }

    public static function validDataProvider(): array
    {
        return [
            ['http://example.com'],
            ['https://example.com'],
            ['http://example.com/path'],
            ['http://example.com/path?query=1'],
            ['http://example.com/path?query=1&query=2'],
        ];
    }

    public static function inValidDataProvider(): array
    {
        return [
            [''],
            ['example.com'],
            ['http:///example.com']
        ];
    }
}