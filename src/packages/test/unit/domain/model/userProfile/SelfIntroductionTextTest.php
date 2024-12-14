<?php

use packages\domain\model\userProfile\SelfIntroductionText;
use PHPUnit\Framework\TestCase;

class SelfIntroductionTextTest extends TestCase
{
    public function test_501文字以上の自己紹介文が入力された場合に例外が発生する()
    {
        // given
        // 501文字以上の自己紹介文
        $selfIntroductionText = str_repeat('あ', 501);

        // when・then
        $this->expectException(InvalidArgumentException::class);
        new SelfIntroductionText($selfIntroductionText);
    }

    public function test_500文字以下の自己紹介文が入力された場合は、正常にインスタンスを生成できる()
    {
        // given
        // 500文字以下の自己紹介文
        $selfIntroductionTextString = str_repeat('あ', 500);

        // when
        $selfIntroductionText = new SelfIntroductionText($selfIntroductionTextString);

        // then
        $this->assertEquals($selfIntroductionTextString, $selfIntroductionText->value);
    }

    public function test_空文字列が入力された場合は、正常にインスタンスを生成できる()
    {
        // given
        // 空文字列
        $selfIntroductionTextString = '';

        // when
        $selfIntroductionText = new SelfIntroductionText($selfIntroductionTextString);

        // then
        $this->assertEquals($selfIntroductionTextString, $selfIntroductionText->value);
    }
}