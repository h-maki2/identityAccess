<?php

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\User as EloquentUser;
use App\Models\definitiveRegistrationConfirmation as EloquentDefinitiveRegistrationConfirmation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\email\LaravelEmailSender;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\transactionManage\TestTransactionManage;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    private EloquentDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private IEmailSender $emailSender;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->definitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);

        $emailSender = $this->createMock(IEmailSender::class);
        $emailSender
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->capturedSendEmailDto = $sendEmailDto;
                return true;
            }));
        $this->emailSender = $emailSender;

        $this->userRegistration = new UserRegistration(
            $this->authenticationAccountRepository,
            $this->definitiveRegistrationConfirmationRepository,
            $this->transactionManage,
            $this->emailSender
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationInformation::query()->delete();
        EloquentDefinitiveRegistrationConfirmation::query()->delete();
        EloquentUser::query()->delete();
    }

    public function test_ユーザー登録が成功する()
    {
        // given
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('abcABC123!');
        $oneTimeToken = OneTimeToken::create();

        // when
        // ユーザー登録を行う
        $this->userRegistration->handle($userEmail, $userPassword, $oneTimeToken);

        // then
        // 未認証状態の認証アカウントが登録されていることを確認する
        $actualAuthenticationAccount = $this->authenticationAccountRepository->findByEmail($userEmail);
        $this->assertEquals(DefinitiveRegistrationCompletedStatus::Incomplete, $actualAuthenticationAccount->DefinitiveRegistrationCompletedStatus());

        // 認証確認情報が登録されていることを確認する
        $actualDefinitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeToken->tokenValue());
        $this->assertNotEmpty($actualDefinitiveRegistrationConfirmation);

        // メール送信する内容が正しいことを確認する
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualDefinitiveRegistrationConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
        $this->assertStringContainsString($actualDefinitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value, $this->capturedSendEmailDto->templateVariables['DefinitiveRegistrationCompletedUpdateUrl']);
    }
}