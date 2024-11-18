<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\userRegistration\IUserRegistrationCompletionEmail;
use packages\application\userRegistration\UserRegistrationApplicationService;
use packages\test\helpers\unitOfWork\TestUnitOfWork;
use PHPUnit\Framework\TestCase;

class UserRegistrationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private TestUnitOfWork $unitOfWork;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->unitOfWork = new TestUnitOfWork();
    }

    public function test_適切なメールアドレスとパスワードの場合、ユーザー登録が行える()
    {
        // given
        $userEmailString = 'test@exmaple.com';
        $userPasswordString = 'ABCabc123_';

        $userRegistrationCompletionEmail = $this->createMock(IUserRegistrationCompletionEmail::class);
        $capturedToAddress = null;
        $capturedOneTimeToken = null;
        $capturedOneTimePassword = null;
        $userRegistrationCompletionEmail->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($sendEmailDto) use (&$capturedToAddress, &$capturedOneTimeToken, &$capturedOneTimePassword) {
                $capturedToAddress = $sendEmailDto->toAddress;
                $capturedOneTimeToken = $sendEmailDto->templateVariables['oneTimeToken'];
                $capturedOneTimePassword = $sendEmailDto->templateVariables['oneTimePassword'];
                return true;
            }));
        
        $userRegistrationApplicationService = new UserRegistrationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformaionRepository,
            $this->unitOfWork,
            $userRegistrationCompletionEmail
        );

        // when
        $result = $userRegistrationApplicationService->userRegister($userEmailString, $userPasswordString);

        // then
        // 登録が成功していることを確認
        $this->assertTrue($result->isSuccess);
        // バリデーションエラーがないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessageList);
        // ワンタイムトークンが生成されていることを確認
        $this->assertNotEmpty($result->oneTimeToken);

        // メール送信の引数が正しいことを確認
        $this->assertEquals($capturedOneTimeToken, $result->oneTimeToken);
        $this->assertEquals($userEmailString, $capturedToAddress);
        $this->assertNotEmpty($capturedOneTimePassword);
    }
}