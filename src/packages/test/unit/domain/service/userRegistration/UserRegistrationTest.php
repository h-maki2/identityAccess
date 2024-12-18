<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private TestTransactionManage $transactionManage;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;
    private IEmailSender $emailSender;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->transactionManage = new TestTransactionManage();
        
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
    }

    public function test_ユーザー登録が成功する()
    {
        // given
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('ABCabc123_');
        $oneTimeToken = OneTimeToken::create();

        // when
        $this->userRegistration->handle($userEmail, $userPassword, $oneTimeToken);

        // then
        // ユーザーが未認証状態で登録されていることを確認
        $actualAuthInfo = $this->authenticationAccountRepository->findByEmail($userEmail);
        $this->assertEquals(VerificationStatus::Unverified, $actualAuthInfo->verificationStatus());
        $this->assertEquals($userPassword, $actualAuthInfo->password());

        // 認証確認情報が保存されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeToken->tokenValue());
        $this->assertNotEmpty($actualAuthConfirmation);

        // メール送信する内容が正しいことを確認する
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualAuthConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
        $this->assertStringContainsString($actualAuthConfirmation->oneTimeToken()->tokenValue()->value, $this->capturedSendEmailDto->templateVariables['verifiedUpdateUrl']);
    }
}