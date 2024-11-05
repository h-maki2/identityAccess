<?php

use packages\domain\model\userProfile\AuthenticationLimitation;
use PHPUnit\Framework\TestCase;

class AuthenticationLimitationTest extends TestCase
{
    public function test_初期化する()
    {
        // given

        // when
        $authenticationLimitation = AuthenticationLimitation::initialization();

        // then
        // ログイン失敗回数は0回である
        
    }
}