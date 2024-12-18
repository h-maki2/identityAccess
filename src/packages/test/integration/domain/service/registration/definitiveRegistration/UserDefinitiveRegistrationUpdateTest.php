<?php

namespace packages\domain\service\registration\definitiveRegistration;

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\User as EloquentUser;
use App\Models\DefinitiveRegistrationConfirmation as EloquentDefinitiveRegistrationConfirmation;
use DateTimeImmutable;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class UserDefinitiveRegistrationUpdateTest extends TestCase
{
    private EloquentDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private UserDefinitiveRegistrationUpdate $UserDefinitiveRegistrationUpdate;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->definitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
        $transactionManage = new EloquentTransactionManage();
        $this->UserDefinitiveRegistrationUpdate = new UserDefinitiveRegistrationUpdate(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $transactionManage
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationInformation::query()->delete();
        EloquentDefinitiveRegistrationConfirmation::query()->delete();
        EloquentUser::query()->delete();
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードが入力された場合に、認証アカウントを本登録済みに更新できる()
    {
        // given
        // 認証アカウントと本登録確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            definitiveRegistrationCompletedStatus: definitiveRegistrationCompletedStatus::Incomplete
        );
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authInfo->id());

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();

        // when
        $this->UserDefinitiveRegistrationUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        // then
        // 認証アカウントが本登録済みに更新されていることを確認
        $actualAuthAccount = $this->authenticationAccountRepository->findById($authInfo->id(), UnsubscribeStatus::Subscribed);
        $this->assertTrue($actualAuthAccount->hasCompletedRegistration());

        // 本登録確認情報が削除されていることを確認
        $this->assertNull($this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue));
    }

    public function test_ワンタイムパスワードが正しくない場合に、認証アカウントを本登録済みに更新できない()
    {
        // given
        // 認証アカウントと本登録確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            definitiveRegistrationCompletedStatus: definitiveRegistrationCompletedStatus::Incomplete
        );
        $oneTimePassword = OneTimePassword::create('111111');
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimePassword: $oneTimePassword
        );

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        // 正しくないワンタイムパスワードを入力する
        $oneTimePassword = OneTimePassword::reconstruct('666666');

        // when・then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを本登録済みに更新できませんでした。');
        $this->UserDefinitiveRegistrationUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }

    public function test_ワンタイムトークンの有効期限が切れている場合に、認証アカウントを本登録済みに更新できない()
    {
        // given
        // 認証アカウントと本登録確認情報を作成する
        $authInfo = $this->authenticationAccountTestDataCreator->create(
            definitiveRegistrationCompletedStatus: definitiveRegistrationCompletedStatus::Incomplete
        );
        // 有効期限が切れているワンタイムトークンを生成
        $oneTimeTokenExpiration = OneTimeTokenExpiration::reconstruct(new DateTimeImmutable('-1 day'));
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $authInfo->id(),
            oneTimeTokenExpiration: $oneTimeTokenExpiration
        );

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->tokenValue();
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();

        // when
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('認証アカウントを本登録済みに更新できませんでした。');
        $this->UserDefinitiveRegistrationUpdate->handle($oneTimeTokenValue, $oneTimePassword);
    }
}