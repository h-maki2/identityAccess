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

class DefinitiveRegistrationUpdateTest extends TestCase
{
    private EloquentDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationUpdate $definitiveRegistrationUpdate;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->definitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
        $transactionManage = new EloquentTransactionManage();
        $this->definitiveRegistrationUpdate = new DefinitiveRegistrationUpdate(
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
        $authToken = $this->authenticationAccountTestDataCreator->create(
            definitiveRegistrationCompletedStatus: DefinitiveRegistrationCompletedStatus::Incomplete
        );
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create($authToken->id());

        $oneTimeToken = $definitiveRegistrationConfirmation->oneTimeToken();
        $oneTimeTokenValue = $oneTimeToken->TokenValue();
        $oneTimePassword = $definitiveRegistrationConfirmation->oneTimePassword();

        // when
        $this->definitiveRegistrationUpdate->handle($oneTimeTokenValue, $oneTimePassword);

        // then
        // 認証アカウントが本登録済みに更新されていることを確認
        $actualAuthAccount = $this->authenticationAccountRepository->findById($authToken->id(), UnsubscribeStatus::Subscribed);
        $this->assertTrue($actualAuthAccount->hasCompletedRegistration());

        // 本登録確認情報が削除されていることを確認
        $this->assertNull($this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue));
    }
}