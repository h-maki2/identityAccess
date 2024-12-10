<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class DisplayVerifiedUpdatePageControllerTest extends TestCase
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

    public function test_認証済み更新ページを表示できる()
    {
        // given
        // 未認証の認証情報を作成して保存する
        $userId = $this->eloquentAuthenticationInformationRepository->nextUserId();
        $this->authenticationInformationTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $expectedAuthConfirmation = $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 認証済み更新ページを表示する
        $response = $this->get('/api//verifiedUpdate?oneTimeTokenValue=' . $expectedAuthConfirmation->oneTimeToken()->value());

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'validationErrorMessage' => '',
            'oneTimeTokenValue' => $expectedAuthConfirmation->oneTimeToken()->value()
        ]);
    }
}