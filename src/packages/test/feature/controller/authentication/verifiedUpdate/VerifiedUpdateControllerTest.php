<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authenticationAccount\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class VerifiedUpdateControllerTest extends TestCase
{
    private EloquentAuthConfirmationRepository $authConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authConfirmationRepository = new EloquentAuthConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationAccountRepository);
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードを入力して、確認済み更新を行う()
    {
        // given
        // 認証情報を作成して保存する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $authConfirmation = $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 確認済み更新を行う
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