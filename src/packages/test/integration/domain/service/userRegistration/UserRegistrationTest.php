<?php

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\AuthConfirmation as EloquentAuthConfirmation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\email\LaravelEmailSender;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use packages\test\helpers\transactionManage\TestTransactionManage;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private IEmailSender $emailSender;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->transactionManage = new TestTransactionManage();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);

        $emailSender = $this->createMock(IEmailSender::class);
        $emailSender
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->capturedSendEmailDto = $sendEmailDto;
                return true;
            }));
        $this->emailSender = $emailSender;

        $this->userRegistration = new UserRegistration(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->transactionManage,
            $this->emailSender
        );

        // テスト前にデータを全削除する
        EloquentAuthenticationInformation::query()->delete();
        EloquentAuthConfirmation::query()->delete();
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
        // 未認証状態の認証情報が登録されていることを確認する
        $actualAuthenticationInformation = $this->authenticationInformationRepository->findByEmail($userEmail);
        $this->assertEquals(VerificationStatus::Unverified, $actualAuthenticationInformation->verificationStatus());

        // 認証確認情報が登録されていることを確認する
        $actualAuthConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeToken->tokenValue());
        $this->assertNotEmpty($actualAuthConfirmation);

        // メール送信する内容が正しいことを確認する
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualAuthConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
        $this->assertStringContainsString($actualAuthConfirmation->oneTimeToken()->tokenValue()->value, $this->capturedSendEmailDto->templateVariables['verifiedUpdateUrl']);
    }
}