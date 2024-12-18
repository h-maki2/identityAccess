<?php

namespace packages\domain\service\verifiedUpdate;

use App\Models\authenticationAccount as EloquentAuthenticationAccount;
use App\Models\AuthConfirmation as EloquentAuthConfirmation;
use DateTimeImmutable;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class VerifiedUpdateTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private VerifiedUpdate $verifiedUpdate;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationAccountRepository);
        $transactionManage = new EloquentTransactionManage();
        $this->verifiedUpdate = new VerifiedUpdate(
            $this->authenticationAccountRepository,
            $this->authConfirmationRepository,
            $transactionManage
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationAccount::query()->delete();
        EloquentAuthConfirmation::query()->delete();
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードが入力された場合に、認証アカウントを確認済みに更新できる()
    {
        // given
        // 認証アカウントと認証確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified
        );
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        $oneTimePassword = $authConfirmation->oneTimePassword();

        // when
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        // then
        // 認証アカウントが確認済みに更新されていることを確認
        $actualAuthInfo = $this->authenticationAccountRepository->findById($authInfo->id());
        $this->assertTrue($actualAuthInfo->isVerified());

        // 認証確認情報が削除されていることを確認
        $this->assertNull($this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue));
    }

    public function test_ワンタイムパスワードが正しくない場合に、認証アカウントを確認済みに更新できない()
    {
        // given
        // 認証アカウントと認証確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified
        );
        $oneTimePassword = OneTimePassword::create('111111');
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimePassword: $oneTimePassword
        );

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        // 正しくないワンタイムパスワードを入力する
        $oneTimePassword = OneTimePassword::reconstruct('666666');

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを確認済みに更新できませんでした。');
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }

    public function test_ワンタイムトークンの有効期限が切れている場合に、認証アカウントを確認済みに更新できない()
    {
        // given
        // 認証アカウントと認証確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified
        );
        // 有効期限が切れているワンタイムトークンを生成
        $oneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'));
        $authConfirmation = $this->authConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimeTokenExpiration: $oneTimeTokenExpiration
        );

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->tokenValue();
        $oneTimePassword = $authConfirmation->oneTimePassword();

        // when
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを確認済みに更新できませんでした。');
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }
}