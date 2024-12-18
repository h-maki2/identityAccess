<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailApplicationService;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailOutputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\SendEmailDto;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use PHPUnit\Framework\TestCase;

class ResendRegistrationConfirmationEmailApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private ResendRegistrationConfirmationEmailApplicationService $resendRegistrationConfirmationEmailApplicationService;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private ResendRegistrationConfirmationEmailResult $catchedResult;
    private IEmailSender $emailSender;
    private SendEmailDto $catchedSendEmailDto;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();

        $emailSender = $this->createMock(IEmailSender::class);
        $emailSender
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->catchedSendEmailDto = $sendEmailDto;
                return true;
            }));
        $this->emailSender = $emailSender;

        $this->resendRegistrationConfirmationEmailApplicationService = new ResendRegistrationConfirmationEmailApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationAccountRepository,
            $this->emailSender
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationAccountRepository);
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
    }

    public function test_認証情報が確認済みではない場合、ワンタイムトークンとワンタイムパスワードの再生成ができる()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 確認済みではない
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationAccount->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $result = $this->resendRegistrationConfirmationEmailApplicationService->resendRegistrationConfirmationEmail($userEmail->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessage);

        // ワンタイムパスワードが再生成されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($authenticationAccount->id());
        $this->assertNotEquals($oneTimePasword->value, $actualAuthConfirmation->oneTimePassword()->value);

        // 正しいデータで本登録確認メールが送信できていることを確認
        $this->assertStringContainsString($actualAuthConfirmation->oneTimeToken()->value(), $this->catchedSendEmailDto->templateVariables['verifiedUpdateUrl']);
        $this->assertEquals($this->catchedSendEmailDto->templateVariables['oneTimePassword'], $actualAuthConfirmation->oneTimePassword()->value);
    }

    public function test_入力されたメールアドレスに紐づく認証情報が存在しない場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create(
            email: $userEmail
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationAccount->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $正しくないメールアドレス = 'other@example.com';
        $result = $this->resendRegistrationConfirmationEmailApplicationService->resendRegistrationConfirmationEmail($正しくないメールアドレス);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('メールアドレスが登録されていません。', $result->validationErrorMessage);
    }

    public function test_認証情報がすでに確認済みの場合、バリデーションエラーが発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Verified // 確認済み
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationAccount->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $result = $this->resendRegistrationConfirmationEmailApplicationService->resendRegistrationConfirmationEmail($userEmail->value);

        // then
        // バリデーションエラーが発生していることを確認
        $this->assertTrue($result->validationError);
        $this->assertEquals('既にアカウントが確認済みです。', $result->validationErrorMessage);
    }

    public function test_認証情報に紐づく認証確認情報が存在しない場合は例外が発生する()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 確認済みではない
        );

        // when・then
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('認証情報が存在しません。userId: ' . $authenticationAccount->id()->value);
        $this->resendRegistrationConfirmationEmailApplicationService->resendRegistrationConfirmationEmail($userEmail->value);
    }
}