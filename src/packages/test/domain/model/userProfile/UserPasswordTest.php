<?php
declare(strict_types=1);

use packages\domain\model\userProfile\UserPassword;
use packages\domain\service\common\Argon2Hash;
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