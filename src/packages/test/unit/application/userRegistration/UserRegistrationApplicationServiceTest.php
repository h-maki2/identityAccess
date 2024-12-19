<?php

use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\email\SendEmailDto;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\application\registration\userProvisionalRegistration\UserProvisionalRegistrationApplicationService;
use packages\domain\model\email\IEmailSender;
use packages\test\helpers\transactionManage\TestTransactionManage;
use PHPUnit\Framework\TestCase;

class UserProvisionalRegistrationApplicationServiceTest extends TestCase
{
    private InMemoryDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private TestTransactionManage $transactionManage;
    private SendEmailDto $capturedSendEmailDto;
    private UserProvisionalRegistrationApplicationService $userProvisionalRegistrationApplicationService;
    private IEmailSender $emailSender;

    public function setUp(): void
    {
        $this->definitiveRegistrationConfirmationRepository = new InMemoryDefinitiveRegistrationConfirmationRepository();
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

        $this->userProvisionalRegistrationApplicationService = new UserProvisionalRegistrationApplicationService(
            $this->definitiveRegistrationConfirmationRepository,
            $this->authenticationAccountRepository,
            $this->transactionManage,
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
        $result = $this->userProvisionalRegistrationApplicationService->userRegister($userEmailString, $userPasswordString, $userPasswordConfirmationString);

        // then
        // バリデーションエラーがないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessageList);

        // メール送信するデータが正しいことを確認
        $this->assertNotEmpty($this->capturedSendEmailDto->templateVariables['DefinitiveRegistrationCompletedUpdateUrl']);
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
        $result = $this->userProvisionalRegistrationApplicationService->userRegister($userEmailString, $userPasswordString, $userPasswordConfirmationString);

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