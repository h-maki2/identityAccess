<?php

use Lcobucci\JWT\Signer\Key\InMemory;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\FailedLoginCount;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\NextLoginAllowedAt;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserName;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\LoginRestrictionStatus;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\service\AuthenticationInformation\AuthenticationInformationService;
use packages\test\helpers\authenticationInformation\TestAuthenticationInformationFactory;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataFactory;
use PHPUnit\Framework\TestCase;

class AuthenticationInformationTest extends TestCase
{
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;

    public function setUp(): void
    {
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
    }

    public function test_重複したメールアドレスを持つユーザーが存在しない場合、ユーザープロフィールを初期化できる()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $authenticationInformationTestDataFactory = new AuthenticationInformationTestDataFactory($this->authenticationInformationRepository);
        $authenticationInformationTestDataFactory->create($alreadyExistsUserEmail);

        $email = new UserEmail('otheruser@example.com');
        $userId = $this->authenticationInformationRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformationService = new AuthenticationInformationService($this->authenticationInformationRepository);

        // when
        $authenticationInformation = AuthenticationInformation::create(
            $userId,
            $email,
            $password,
            $authenticationInformationService
        );

        // then
        $this->assertEquals(VerificationStatus::Unverified, $authenticationInformation->verificationStatus());
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $authenticationInformation->LoginRestriction()->loginRestrictionStatus());
        $this->assertEquals(0, $authenticationInformation->LoginRestriction()->failedLoginCount());
        $this->assertEquals(null, $authenticationInformation->LoginRestriction()->nextLoginAllowedAt());

        // 以下の属性はそのまま設定される
        $this->assertEquals($email, $authenticationInformation->email());
        $this->assertEquals($userId, $authenticationInformation->id());
        $this->assertEquals($password, $authenticationInformation->password());
    }

    public function test_重複したメールアドレスを持つユーザーが既に存在する場合、ユーザープロフィールを初期化できない()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $authenticationInformationTestDataFactory = new AuthenticationInformationTestDataFactory($this->authenticationInformationRepository);
        $authenticationInformationTestDataFactory->create($alreadyExistsUserEmail);

        // メールアドレスが重複している
        $email = new UserEmail('user@example.com');
        $userId = $this->authenticationInformationRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformationService = new AuthenticationInformationService($this->authenticationInformationRepository);

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('すでに存在するメールアドレスです。');
        AuthenticationInformation::create(
            $userId,
            $email,
            $password,
            $authenticationInformationService
        );
    }

    public function test_ユーザープロフィールを再構築できる()
    {
        // given
        $email = new UserEmail('otheruser@example.com');
        $userId = $this->authenticationInformationRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $verificationStatus = VerificationStatus::Verified;
        $LoginRestriction = LoginRestriction::initialization();

        // when
        $authenticationInformation = AuthenticationInformation::reconstruct(
            $userId,
            $email,
            $password,
            $verificationStatus,
            $LoginRestriction
        );

        // then
        $this->assertEquals($email, $authenticationInformation->email());
        $this->assertEquals($userId, $authenticationInformation->id());
        $this->assertEquals($password, $authenticationInformation->password());
        $this->assertEquals($verificationStatus, $authenticationInformation->verificationStatus());
        $this->assertEquals($LoginRestriction, $authenticationInformation->LoginRestriction());
    }

    public function 認証ステータスを認証済みに更新できる()
    {
        // given
        // 認証済みステータスが未認証のユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Unverified;
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus
        );

        // when
        $authenticationInformation->updateVerified();

        // then
        $this->assertEquals(VerificationStatus::Verified, $authenticationInformation->verificationStatus());
    }

    public function test_認証ステータスが認証済みの場合、パスワードの変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $password = UserPassword::create('124abcABC!');
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            $password,
            $verificationStatus
        );

        // when
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformation->changePassword($passwordAfterChange, new DateTimeImmutable());

        // then
        $this->assertEquals($passwordAfterChange, $authenticationInformation->password());
    }

    public function test_認証ステータスが未認証の場合、パスワードの変更が行えない()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;
        $password = UserPassword::create('124abcABC!');
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            $password,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformation->changePassword($passwordAfterChange, new DateTimeImmutable());
    }

    public function test_アカウントがロックされている場合、パスワードの変更が行えない()
    {
        // given
        // アカウントがロックされているユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $password = UserPassword::create('124abcABC!');
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            $password,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('アカウントがロックされています。');
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformation->changePassword($passwordAfterChange, new DateTimeImmutable());
    }

    public function test_ログイン失敗回数を更新する()
    {
        // given
        $verificationStatus = VerificationStatus::Verified;
        // ログイン失敗回数は0回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformation->addFailedLoginCount();

        // then
        $this->assertEquals(1, $authenticationInformation->LoginRestriction()->failedLoginCount());
    }

    public function test_認証ステータスが未認証の場合、ログイン失敗回数を更新しない()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;
        // ログイン失敗回数は0回
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(0),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $authenticationInformation->addFailedLoginCount();
    }

    public function test_ログイン制限が有効可能の場合、ログイン制限を有効にする()
    {
        // given
        // ログイン失敗回数が10回に達している認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformation->enableLoginRestriction(new DateTimeImmutable());

        // then
        $this->assertEquals(LoginRestrictionStatus::Restricted->value, $authenticationInformation->LoginRestriction()->loginRestrictionStatus());
        $this->assertNotNull($authenticationInformation->LoginRestriction()->nextLoginAllowedAt());
    }

    public function test_認証ステータスが未認証の場合、ログイン制限を有効にできない()
    {
        // given
        $verificationStatus = VerificationStatus::Unverified;
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $authenticationInformation->enableLoginRestriction(new DateTimeImmutable());
    }

    public function test_ログイン制限が有効で再ログイン可能である場合はログイン制限を解除できる()
    {
        // given
        // ログイン制限は有効だが再ログインは可能である認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformation->disableLoginRestriction(new DateTimeImmutable());

        // then
        $this->assertEquals(LoginRestrictionStatus::Unrestricted->value, $authenticationInformation->LoginRestriction()->loginRestrictionStatus());
        $this->assertNull($authenticationInformation->LoginRestriction()->nextLoginAllowedAt());
    }

    public function test_ログイン制限が有効状態で再ログインが不可である場合、ログインができないことを判定できる()
    {
        // given
        // ログイン制限が有効状態で再ログインが不可である認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('+10 minutes'))
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when
        $result = $authenticationInformation->canLoggedIn(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限が有効状態で再ログインが可能である場合、ログインが可能であることを判定できる()
    {
        // given
        // ログイン制限は有効だが再ログイン可能な認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::reconstruct(new DateTimeImmutable('-1 minutes'))
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when
        $result = $authenticationInformation->canLoggedIn(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限が有効状態ではない場合、ログインが可能であることを判定できる()
    {
        // given
        // ログイン制限が有効状態ではない認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when
        $result = $authenticationInformation->canLoggedIn(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_認証ステータスが未認証の場合、ログイン不可であることを判定できる()
    {
        // given
        // 認証ステータスが未認証の認証情報を生成する
        $verificationStatus = VerificationStatus::Unverified;
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus
        );

        // when
        $result = $authenticationInformation->canLoggedIn(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }

    public function test_ログイン制限を有効にできるかどうかを判定できる()
    {
        // given
        // ログイン失敗回数が10回に達していている認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when
        $result = $authenticationInformation->canEnableLoginRestriction(new DateTimeImmutable());

        // then
        $this->assertTrue($result);
    }

    public function test_ログイン制限を有効にできないことを判定できる()
    {
        // given
        // ログイン失敗回数が10回に達していない認証情報を生成する
        $verificationStatus = VerificationStatus::Verified;
        $loginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(9),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $loginRestriction
        );

        // when
        $result = $authenticationInformation->canEnableLoginRestriction(new DateTimeImmutable());

        // then
        $this->assertFalse($result);
    }
}