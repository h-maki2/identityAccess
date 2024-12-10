<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class VerifiedUpdateControllerTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformationRepository);
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードを入力して、認証済み更新を行う()
    {
        // given
        // 認証情報を作成して保存する
        $userId = $this->authenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $authConfirmation = $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 認証済み更新を行う
        $response = $this->post('/api//verifiedUpdate', [
            'oneTimeTokenValue' => $authConfirmation->oneTimeToken()->value(),
            'oneTimePassword' => $authConfirmation->oneTimePassword()->value
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'validationErrorMessage' => ''
        ]);
    }
}