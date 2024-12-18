<?php

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\User as EloquentUser;
use App\Models\AuthConfirmation as EloquentAuthConfirmation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\email\LaravelEmailSender;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\transactionManage\TestTransactionManage;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private IEmailSender $emailSender;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationAccountRepository);

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
            $this->authConfirmationRepository,
            $this->transactionManage,
            $this->emailSender
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationInformation::query()->delete();
        EloquentAuthConfirmation::query()->delete();
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
        $this->assertEquals(VerificationStatus::Unverified, $actualAuthenticationAccount->verificationStatus());

        // 認証確認情報が登録されていることを確認する
        $actualAuthConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeToken->tokenValue());
        $this->assertNotEmpty($actualAuthConfirmation);

        // メール送信する内容が正しいことを確認する
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualAuthConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
        $this->assertStringContainsString($actualAuthConfirmation->oneTimeToken()->tokenValue()->value, $this->capturedSendEmailDto->templateVariables['verifiedUpdateUrl']);
    }
}