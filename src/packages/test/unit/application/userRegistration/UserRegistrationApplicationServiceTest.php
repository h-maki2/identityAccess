<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\common\email\SendEmailDto;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\domain\service\userRegistration\IUserRegistrationCompletionEmail;
use packages\domain\service\userRegistration\UserRegistrationApplicationService;
use packages\domain\service\userRegistration\UserRegistrationOutputBoundary;
use packages\domain\service\userRegistration\UserRegistrationResult;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class UserRegistrationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformationRepository $authenticationInformationRepository;
    private TestUnitOfWork $unitOfWork;
    private UserRegistrationOutputBoundary $outputBoundary;
    private UserRegistrationResult $capturedResult;
    private IUserRegistrationCompletionEmail $userRegistrationCompletionEmail;
    private SendEmailDto $capturedSendEmailDto;
    private UserRegistrationApplicationService $userRegistrationApplicationService;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformationRepository = new InMemoryAuthenticationInformationRepository();
        $this->unitOfWork = new TestUnitOfWork();

        $outputBoundary = $this->createMock(UserRegistrationOutputBoundary::class);
        $outputBoundary
            ->method('present')
            ->with($this->callback(function (UserRegistrationResult $capturedResult) {
                $this->capturedResult = $capturedResult;
                return true;
            }));
        $this->outputBoundary = $outputBoundary;

        $userRegistrationCompletionEmail = $this->createMock(IUserRegistrationCompletionEmail::class);
        $userRegistrationCompletionEmail
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->capturedSendEmailDto = $sendEmailDto;
                return true;
            }));

        $this->userRegistrationApplicationService = new UserRegistrationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformationRepository,
            $this->unitOfWork,
            $userRegistrationCompletionEmail,
            $this->outputBoundary
        );
    }

    public function test_適切なメールアドレスとパスワードの場合、ユーザー登録が行える()
    {
        // given
        $userEmailString = 'test@exmaple.com';
        $userPasswordString = 'ABCabc123_';

        // when
        $this->userRegistrationApplicationService->userRegister($userEmailString, $userPasswordString);

        // then
        // バリデーションエラーがないことを確認
        $this->assertFalse($this->capturedResult->validationError);
        $this->assertEmpty($this->capturedResult->validationErrorMessageList);

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

        // when
        $this->userRegistrationApplicationService->userRegister($userEmailString, $userPasswordString);

        // then
        // バリデーションエラーがあることを確認
        $this->assertTrue($this->capturedResult->validationError);
        // バリデーションエラーメッセージが正しいことを確認
        $expectedErrorMessageDataList = [
            new ValidationErrorMessageData('email', ['不正なメールアドレスです。']),
            new ValidationErrorMessageData('password', [
                'パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください'
            ])
        ];
        $this->assertEquals($expectedErrorMessageDataList, $this->capturedResult->validationErrorMessageList);
    }
}