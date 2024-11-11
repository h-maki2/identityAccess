<?php

use Lcobucci\JWT\Signer\Key\InMemory;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\FailedLoginCount;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\NextLoginAllowedAt;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserName;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\LoginRestrictionStatus;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;
use packages\test\helpers\authenticationInformaion\TestAuthenticationInformaionFactory;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataFactory;
use PHPUnit\Framework\TestCase;

class AuthenticationInformaionTest extends TestCase
{
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function setUp(): void
    {
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
    }

    public function test_重複したメールアドレスを持つユーザーが存在しない場合、ユーザープロフィールを初期化できる()
    {
        // given
        // user@example.comのアドレスを持つユーザーをあらかじめ作成しておく
        $alreadyExistsUserEmail = new UserEmail('user@example.com');
        $authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->authenticationInformaionRepository);
        $authenticationInformaionTestDataFactory->create($alreadyExistsUserEmail);

        $email = new UserEmail('otheruser@example.com');
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformaionService = new AuthenticationInformaionService($this->authenticationInformaionRepository);

        // when
        $authenticationInformaion = AuthenticationInformaion::create(
            $userId,
            $email,
            $password,
            $authenticationInformaionService
        );

        // then
        $this->assertEquals(VerificationStatus::Unverified, $authenticationInformaion->verificationStatus());
        $this->assertEquals(LoginRestrictionStatus::Unrestricted, $authenticationInformaion->LoginRestriction()->loginRestrictionStatus());
        $this->assertEquals(0, $authenticationInformaion->LoginRestriction()->failedLoginCount());
        $this->assertEquals(null, $authenticationInformaion->LoginRestriction()->nextLoginAllowedAt());

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
        $authenticationInformaionTestDataFactory = new AuthenticationInformaionTestDataFactory($this->authenticationInformaionRepository);
        $authenticationInformaionTestDataFactory->create($alreadyExistsUserEmail);

        // メールアドレスが重複している
        $email = new UserEmail('user@example.com');
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $authenticationInformaionService = new AuthenticationInformaionService($this->authenticationInformaionRepository);

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
        $userId = $this->authenticationInformaionRepository->nextUserId();
        $password = UserPassword::create('1234abcABC!');
        $verificationStatus = VerificationStatus::Verified;
        $LoginRestriction = LoginRestriction::initialization();

        // when
        $authenticationInformaion = AuthenticationInformaion::reconstruct(
            $userId,
            $email,
            $password,
            $verificationStatus,
            $LoginRestriction
        );

        // then
        $this->assertEquals($email, $authenticationInformaion->email());
        $this->assertEquals($userId, $authenticationInformaion->id());
        $this->assertEquals($password, $authenticationInformaion->password());
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
            $verificationStatus
        );

        // when
        $authenticationInformaion->updateVerified();

        // then
        $this->assertEquals(VerificationStatus::Verified, $authenticationInformaion->verificationStatus());
    }

    public function test_認証ステータスが認証済みの場合、パスワードの変更が行える()
    {
        // given
        // 認証済みステータスが認証済みのユーザープロフィールを作成
        $verificationStatus = VerificationStatus::Verified;
        $password = UserPassword::create('124abcABC!');
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
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
            $password,
            $verificationStatus
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $passwordAfterChange = UserPassword::create('124abcABC!_afterChange');
        $authenticationInformaion->changePassword($passwordAfterChange, new DateTimeImmutable());
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
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
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
        $authenticationInformaion->changePassword($passwordAfterChange, new DateTimeImmutable());
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
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformaion->addFailedLoginCount();

        // then
        $this->assertEquals(1, $authenticationInformaion->LoginRestriction()->failedLoginCount());
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
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証済みのユーザーではありません。');
        $authenticationInformaion->addFailedLoginCount();
    }

    public function test_ログイン制限が有効可能の場合、ログイン制限を有効にする()
    {
        // given
        // ログイン失敗回数が10回で、ログイン制限が有効な認証情報を生成
        $verificationStatus = VerificationStatus::Verified;
        $LoginRestriction = LoginRestriction::reconstruct(
            FailedLoginCount::reconstruct(10),
            LoginRestrictionStatus::Unrestricted,
            null
        );
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            null,
            null,
            $verificationStatus,
            null,
            $LoginRestriction
        );

        // when
        $authenticationInformaion->enableLoginRestriction(new DateTimeImmutable());

        // then
        $this->assertEquals(LoginRestrictionStatus::Restricted, $authenticationInformaion->LoginRestriction()->loginRestrictionStatus());
        $this->assertNotNull($authenticationInformaion->LoginRestriction()->nextLoginAllowedAt());
    }
}