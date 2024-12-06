<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\email\SendEmailDto;
use packages\domain\service\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\domain\service\userRegistration\UserRegistration;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestUnitOfWork $unitOfWork;
    private IUserRegistrationCompletionEmail $userRegistrationCompletionEmail;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistration $userRegistration;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->unitOfWork = new TestUnitOfWork();

        $userRegistrationCompletionEmail = $this->createMock(IUserRegistrationCompletionEmail::class);
        $userRegistrationCompletionEmail
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->capturedSendEmailDto = $sendEmailDto;
                return true;
            }));

        $this->userRegistrationCompletionEmail = $userRegistrationCompletionEmail;

        $this->userRegistration = new UserRegistration(
            $this->authenticationInformationRepository,
            $this->authConfirmationRepository,
            $this->unitOfWork,
            $this->userRegistrationCompletionEmail
        );
    }

    public function test_ユーザー登録が成功する()
    {
        // given
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('ABCabc123_');

        // when
        $this->userRegistration->handle($userEmail, $userPassword);

        // then
        // ユーザーが未認証状態で登録されていることを確認
        $actualAuthInfo = $this->authenticationInformationRepository->findByEmail($userEmail);
        $this->assertEquals(VerificationStatus::Unverified, $actualAuthInfo->verificationStatus());
        $this->assertEquals($userPassword, $actualAuthInfo->password());

        // 認証確認情報が保存されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($actualAuthInfo->id());
        $this->assertNotNull($actualAuthConfirmation);

        // メールが送信されていることを確認
        $this->assertEquals($userEmail->value, $this->capturedSendEmailDto->toAddress);
        $this->assertEquals($actualAuthConfirmation->oneTimeToken()->value(), $this->capturedSendEmailDto->templateVariables['oneTimeToken']);
        $this->assertEquals($actualAuthConfirmation->oneTimePassword()->value, $this->capturedSendEmailDto->templateVariables['oneTimePassword']);
    }
}