<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class ResendRegistrationConfirmationEmailControllerTest extends TestCase
{
    private EloquentAuthenticationInformationRepository $eloquentAuthenticationInformationRepository;
    private EloquentAuthConfirmationRepository $eloquentAuthConfirmationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentAuthenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->eloquentAuthConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->eloquentAuthenticationInformationRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->eloquentAuthConfirmationRepository, $this->eloquentAuthenticationInformationRepository);
    }

    public function test_登録済みのメールアドレスの場合に、本登録確認メールを再送信できる()
    {
        // given
        // 未認証の認証情報を作成して保存する
        $userEmail = new UserEmail('hello@example.com');
        $userId = $this->eloquentAuthenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            email: $userEmail, 
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 本登録確認メールを再送信する
        $response = $this->post('/resendRegistrationConfirmationEmail', ['email' => $userEmail->value]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'validationErrorMessage' => ''
        ]);
    }
}