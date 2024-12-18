<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class ResendRegistrationConfirmationEmailControllerTest extends TestCase
{
    private EloquentAuthenticationAccountRepository $eloquentAuthenticationAccountRepository;
    private EloquentAuthConfirmationRepository $eloquentAuthConfirmationRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentAuthenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->eloquentAuthConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->eloquentAuthenticationAccountRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->eloquentAuthConfirmationRepository, $this->eloquentAuthenticationAccountRepository);
    }

    public function test_登録済みのメールアドレスの場合に、本登録確認メールを再送信できる()
    {
        // given
        // 未認証の認証アカウントを作成して保存する
        $userEmail = new UserEmail('hello@example.com');
        $userId = $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            email: $userEmail, 
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 本登録確認メールを再送信する
        $response = $this->post('/api//resendRegistrationConfirmationEmail', ['email' => $userEmail->value]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'validationErrorMessage' => ''
        ]);
    }
}