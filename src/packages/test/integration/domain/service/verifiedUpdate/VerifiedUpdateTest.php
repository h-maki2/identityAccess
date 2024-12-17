<?php

namespace packages\domain\service\verifiedUpdate;

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\AuthConfirmation as EloquentAuthConfirmation;
use DateTimeImmutable;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class VerifiedUpdateTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private VerifiedUpdate $verifiedUpdate;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);
        $transactionManage = new EloquentTransactionManage();
        $this->verifiedUpdate = new VerifiedUpdate(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $transactionManage
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationInformation::query()->delete();
        EloquentAuthConfirmation::query()->delete();
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードが入力された場合に、認証情報を認証済みに更新できる()
    {
        // given
        // 認証情報と認証確認情報を作成する
        $authInfo = $this->authenticationInformationTestDataCreator->create(
            verificationStatus: VerificationStatus::Unverified
        );
        $authConfirmation = $this->authConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $authConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        $oneTimePassword = $authConfirmation->oneTimePassword();

        // when
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        // then
        // 認証情報が認証済みに更新されていることを確認
        $actualAuthInfo = $this->authenticationInformationRepository->findById($authInfo->id());
        $this->assertTrue($actualAuthInfo->isVerified());

        // 認証確認情報が削除されていることを確認
        $this->assertNull($this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue));
    }

    public function test_ワンタイムパスワードが正しくない場合に、認証情報を認証済みに更新できない()
    {
        // given
        // 認証情報と認証確認情報を作成する
        $authInfo = $this->authenticationInformationTestDataCreator->create(
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
        $this->expectExceptionMessage('認証情報を認証済みに更新できませんでした。');
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }

    public function test_ワンタイムトークンの有効期限が切れている場合に、認証情報を認証済みに更新できない()
    {
        // given
        // 認証情報と認証確認情報を作成する
        $authInfo = $this->authenticationInformationTestDataCreator->create(
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
        $this->expectExceptionMessage('認証情報を認証済みに更新できませんでした。');
        $this->verifiedUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }
}