<?php

use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\validation\AuthConfirmationValidation;
use packages\test\helpers\authConfirmation\TestAuthConfirmationFactory;
use packages\test\helpers\authConfirmation\TestOneTimeTokenFactory;
use PHPUnit\Framework\TestCase;

class AuthConfirmationValidationTest extends TestCase
{
    public function test_認証確認がnullの場合は、validateメソッドの戻り値はfalse()
    {
        // given
        $authConfirmation = null;

        // when
        $result = AuthConfirmationValidation::validate($authConfirmation, new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_認証確認情報の有効期限が切れている場合は、validateメソッドの戻り値はfalse()
    {
        // given
        // 有効期限が切れているワンタイムトークンを作成
        $oenTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            expiration: OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'))
        );
        // 有効期限が切れている認証確認を作成
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation(
            oneTimeToken: $oenTimeToken
        );

        // when
        $result = AuthConfirmationValidation::validate($authConfirmation, new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_有効な認証確認の場合は、validateメソッドの戻り値はtrue()
    {
        // given
        // 有効期限が切れていないワンタイムトークンを作成
        $oenTimeToken = TestOneTimeTokenFactory::createOneTimeToken(
            expiration: OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('+1 day'))
        );
        // 有効期限が切れていない認証確認を作成
        $authConfirmation = TestAuthConfirmationFactory::createAuthConfirmation(
            oneTimeToken: $oenTimeToken
        );

        // when
        $result = AuthConfirmationValidation::validate($authConfirmation, new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }
}