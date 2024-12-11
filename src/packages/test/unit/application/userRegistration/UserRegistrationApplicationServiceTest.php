<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\email\SendEmailDto;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\application\userRegistration\UserRegistrationApplicationService;
use packages\domain\model\email\IEmailSender;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class UserRegistrationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestUnitOfWork $unitOfWork;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistrationApplicationService $userRegistrationApplicationService;
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

        $this->userRegistrationApplicationService = new UserRegistrationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformationRepository,
            $this->unitOfWork,
            $this->emailSender
        );
    }

    public function test_適切なメールアドレスとパスワードの場合、ユーザー登録が行える()
    {
        // given
        $userEmailString = 'test@exmaple.com';
        $userPasswordString = 'ABCabc123_';
        $userPasswordConfirmationString = 'ABCabc123_';

        // when
        $result = $this->userRegistrationApplicationService->userRegister($userEmailString, $userPasswordString, $userPasswordConfirmationString);

        // then
        // バリデーションエラーがないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessageList);

        // メール送信するデータが正しいことを確認
        $this->assertNotEmpty($this->capturedSendEmailDto->templateVariables['oneTimeToken']);
        $this->assertEquals($userEmailString, $this->capturedSendEmailDto->toAddress);
        $this->assertNotEmpty($this->capturedSendEmailDto->templateVariables['oneTimePassword']);
    }

    public function test_バリデーションエラーが発生した場合に、ユーザー登録が失敗する()
    {
        // given
        // メールアドレスの形式が不正な場合
        $userEmailString = 'test';
        // パスワードの形式が不正な場合
        $userPasswordString = 'password';
        // パスワード確認が一致しない場合
        $userPasswordConfirmationString = 'ABCabc123_';

        // when
        $result = $this->userRegistrationApplicationService->userRegister($userEmailString, $userPasswordString, $userPasswordConfirmationString);

        // then
        // バリデーションエラーがあることを確認
        $this->assertTrue($result->validationError);
        // バリデーションエラーメッセージが正しいことを確認
        $expectedErrorMessageDataList = [
            new ValidationErrorMessageData('email', ['不正なメールアドレスです。']),
            new ValidationErrorMessageData('password', [
                'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
            ]),
            new ValidationErrorMessageData('passwordConfirmation', [
                'パスワードが一致しません。'
            ])
        ];
        $this->assertEquals($expectedErrorMessageDataList, $result->validationErrorMessageList);
    }
}