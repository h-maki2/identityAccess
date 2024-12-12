<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\model\email\IEmailSender;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestUnitOfWork $unitOfWork;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;
    private IEmailSender $emailSender;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->unitOfWork = new TestUnitOfWork();
        
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
            $this->unitOfWork,
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
        $actualAuthInfo = $this->authenticationInformationRepository->findByEmail($userEmail);
        $this->assertEquals(VerificationStatus::Unverified, $actualAuthInfo->verificationStatus());
        $this->assertEquals($userPassword, $actualAuthInfo->password());

        // 認証確認情報が保存されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($actualAuthInfo->id());
        $this->assertEquals($oneTimeToken->tokenValue(), $actualAuthConfirmation->oneTimeToken()->tokenValue());
        $this->assertEquals($oneTimeToken->expirationDate(), $actualAuthConfirmation->oneTimeToken()->expirationDate());

        // メールが送信されていることを確認
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualAuthConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
        $this->assertStringContainsString($actualAuthConfirmation->oneTimeToken()->tokenValue()->value, $this->capturedSendEmailDto->templateVariables['verifiedUpdateUrl']);
    }
}