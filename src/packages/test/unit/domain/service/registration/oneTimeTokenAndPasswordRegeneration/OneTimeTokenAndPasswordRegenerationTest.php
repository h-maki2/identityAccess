<?php

use App\Models\DefinitiveRegistrationConfirmation;
use Lcobucci\JWT\Signer\Key\InMemory;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\adapter\persistence\inMemory\InMemoryDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\email\IEmailSender;
use packages\domain\model\email\SendEmailDto;
use packages\domain\service\registration\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegeneration;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use PHPUnit\Framework\TestCase;

class OneTimeTokenAndPasswordRegenerationTest extends TestCase
{
    private InMemoryAuthenticationAccountRepository $authenticationAccountRepository;
    private InMemoryDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private OneTimeTokenAndPasswordRegeneration $oneTimeTokenAndPasswordRegeneration;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;
    private SendEmailDto $catchedSendEmailDto;

    public function setUp(): void
    {
        $emailSender = $this->createMock(IEmailSender::class);
        $emailSender
            ->method('send')
            ->with($this->callback(function (SendEmailDto $sendEmailDto) {
                $this->catchedSendEmailDto = $sendEmailDto;
                return true;
            }));
        
        $this->definitiveRegistrationConfirmationRepository = new InMemoryDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new InMemoryAuthenticationAccountRepository();
        $this->oneTimeTokenAndPasswordRegeneration = new OneTimeTokenAndPasswordRegeneration(
            $this->definitiveRegistrationConfirmationRepository,
            $emailSender
        );
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
    }

    public function test_ワンタイムトークンとワンタイムパスワードの再生成後に、正しいデータで本登録確認メールが再送できていることを確認する()
    {
        // given
        // 認証アカウント生成して保存する
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create();

        // 本登録確認情報を生成して保存する
        $this->definitiveRegistrationConfirmationTestDataCreator->create($authenticationAccount->id());

        // when
        // ワンタイムトークンとワンタイムパスワードを再生成する
        $this->oneTimeTokenAndPasswordRegeneration->handle($authenticationAccount);

        // then
        // 本登録確認メールを再送できていることを疑似的に確認する
        $actualDefinitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findById($authenticationAccount->id());
        $this->assertStringContainsString($actualDefinitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value, $this->catchedSendEmailDto->templateVariables['definitiveRegisterUrl']);
        $this->assertEquals($this->catchedSendEmailDto->templateVariables['oneTimePassword'], $actualDefinitiveRegistrationConfirmation->oneTimePassword()->value);
    }

    public function test_認証アカウントに紐づく本登録確認情報が存在しない場合は例外が発生する()
    {
        // given
        // 認証アカウント生成して保存する
        $authenticationAccount = $this->authenticationAccountTestDataCreator->create();
        // 本登録確認情報を生成しな

        // then
        // 例外が発生することを確認
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('認証アカウントが存在しません。userId: ' . $authenticationAccount->id()->value);
        $this->oneTimeTokenAndPasswordRegeneration->handle($authenticationAccount);
    }
}