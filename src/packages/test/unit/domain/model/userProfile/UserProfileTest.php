<?php

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserName;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\service\AuthenticationInformaion\AuthenticationInformaionService;
use packages\test\helpers\AuthenticationInformaion\TestAuthenticationInformaionFactory;
use packages\test\helpers\AuthenticationInformaion\AuthenticationInformaionTestDataFactory;
use PHPUnit\Framework\TestCase;

class AuthenticationInformaionTest extends TestCase
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function setUp(): void
    {
        $this->AuthenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
    }

    public function test_重複したメールアドレスを持つユーザーが存在しない場合、ユーザープロフィールを初期化できる()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->AuthenticationInformaionRepository);
        $authenticationInformaionTestDataFactory->create($alreadyExistsUserEmail);

        $email = new UserEmail('otheruser@example.com');
        $userId = $this->AuthenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformaionService = new AuthenticationInformaionService($this->AuthenticationInformaionRepository);

        // when
        $authenticationInformaion = AuthenticationInformaion::create(
            $userId,
            $email,
            $password,
            $authenticationInformaionService
        );

        // then
        $this->assertEquals('otheruser', $authenticationInformaion->name()->value);
        $this->assertEquals(VerificationStatus::Unverified, $authenticationInformaion->verificationStatus());

        // 以下の属性はそのまま設定される
        $this->assertEquals($email, $authenticationInformaion->email());
        $this->assertEquals($userId, $authenticationInformaion->id());
        $this->assertEquals($password, $authenticationInformaion->password());
    }

    public function test_重複したメールアドレスを持つユーザーが既に存在する場合、ユーザープロフィールを初期化できない()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->AuthenticationInformaionRepository);
        $authenticationInformaionTestDataFactory->create($alreadyExistsUserEmail);

        // メールアドレスが重複している
        $email = new UserEmail('user@example.com');
        $userId = $this->AuthenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformaionService = new AuthenticationInformaionService($this->AuthenticationInformaionRepository);

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('すでに存在するメールアドレスです。');
        AuthenticationInformaion::create(
            $userId,
            $email,
            $password,
            $authenticationInformaionService
        );
    }

    public function test_ユーザープロフィールを再構築できる()
    {
        // given
        $email = new UserEmail('otheruser@example.com');
        $userId = $this->AuthenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $verificationStatus = VerificationStatus::Verified;
        $userName = UserName::create('test user');
        $LoginRestriction = LoginRestriction::initialization();

        // when
        $authenticationInformaion = AuthenticationInformaion::reconstruct(
            $userId,
            $email,
            $userName,
            $password,
            $verificationStatus,
            $LoginRestriction
        );

        // then
        $this->assertEquals($email, $authenticationInformaion->email());
        $this->assertEquals($userId, $authenticationInformaion->id());
        $this->assertEquals($password, $authenticationInformaion->password());
        $this->assertEquals($userName, $authenticationInformaion->name());
        $this->assertEquals($verificationStatus, $authenticationInformaion->verificationStatus());
        $this->assertEquals($LoginRestriction, $authenticationInformaion->LoginRestriction());
    }

    public function 認証ステータスを認証済みに更新できる()
    {
        // given
        // 認証済みステータスが未認証のユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Unverified;
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            null,
            $verificationStatus
        );

        // when
        $authenticationInformaion->updateVerified();

        // then
        $this->assertEquals(VerificationStatus::Verified, $authenticationInformaion->verificationStatus());
    }

    public function test_認証ステータスが認証済みの場合、ユーザー名の変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $userName = UserName::create('test user');
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            $userName,
            null,
            $verificationStatus
        );

        // when
        $userNameAfterChange = UserName::create('test user after change');
        $authenticationInformaion->changeName($userNameAfterChange, new DateTimeImmutable());

        // then
        $this->assertEquals($userNameAfterChange, $authenticationInformaion->name());
    }

    public function test_認証ステータスが未認証の場合、ユーザー名の変更が行えない()
    {
        // given
        // 認証済みステータスが未認証のユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Unverified;
        $userName = UserName::create('test user');
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            $userName,
            null,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $userNameAfterChange = UserName::create('test user after change');
        $authenticationInformaion->changeName($userNameAfterChange, new DateTimeImmutable());
    }

    public function test_認証ステータスが認証済みの場合、パスワードの変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $password = UserPassword::create('124abcABC!');
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            $password,
            $verificationStatus
        );

        // when
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformaion->changePassword($passwordAfterChange, new DateTimeImmutable());

        // then
        $this->assertEquals($passwordAfterChange, $authenticationInformaion->password());
    }

    public function test_認証ステータスが未認証の場合、パスワードの変更が行えない()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;
        $password = UserPassword::create('124abcABC!');
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            $password,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformaion->changePassword($passwordAfterChange, new DateTimeImmutable());
    }

    public function test_ログイン失敗回数を更新する()
    {
        // given
        $verificationStatus = VerificationStatus::Verified;
        // ログイン失敗回数は0回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            null
        );
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformaion->updateFailedLoginCount();

        // then
        $this->assertEquals(1, $authenticationInformaion->LoginRestriction()->failedLoginCount());
    }

    public function test_再ログイン可能な日時を更新する()
    {
        // given
        $verificationStatus = VerificationStatus::Verified;
        // ログイン失敗回数は10回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );
        $expectedNextLoginAllowedAt = NextLoginAllowedAt::create();

        // when
        $authenticationInformaion->updateNextLoginAllowedAt();

        // then
        $this->assertEquals(10, $authenticationInformaion->LoginRestriction()->failedLoginCount());
        $this->assertEquals($expectedNextLoginAllowedAt->formattedValue(), $authenticationInformaion->LoginRestriction()->NextLoginAllowedAt());
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達している場合を判定できる()
    {
        // given
        // ログイン失敗回数は10回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            null
        );
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            null,
            null,
            null,
            $LoginRestriction
        );

        // when
        $result = $authenticationInformaion->hasReachedAccountLockoutThreshold();

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン失敗回数がアカウントロックのしきい値に達していない場合を判定できる()
    {
         // given
        // ログイン失敗回数は9回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            null
        );
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            null,
            null,
            null,
            $LoginRestriction
        );

        // when
        $result = $authenticationInformaion->hasReachedAccountLockoutThreshold();

        // then
        $this->assertFalse($result);
    }
}