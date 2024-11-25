<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\VerificationStatus;
use packages\test\helpers\authenticationInformation\AuthenticationInformationTestDataCreator;
use Tests\TestCase;

class EloquentAuthenticationInformaionRepositoryTest extends TestCase
{
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;
    private AuthenticationInformationTestDataCreator $authenticationInformationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
        $this->authenticationInformationTestDataCreator = new AuthenticationInformationTestDataCreator($this->authenticationInformationRepository);
    }

    public function test_認証情報をインサートできる()
    {
        // given
        // 認証情報を作成する
        $userEmail = new UserEmail('test@example.com');
        $userPassword = UserPassword::create('abcABC123!');
        $verificationStatus = VerificationStatus::Verified;
        $userId = $this->authenticationInformationRepository->nextUserId();
        $loginRestriction = LoginRestriction::initialization();
        $authenticationInformation = $this->authenticationInformationTestDataCreator->create(
            $userEmail,
            $userPassword,
            $verificationStatus,
            $userId,
            $loginRestriction
        );

        // when
        $this->authenticationInformationRepository->save($authenticationInformation);

        $this->assertTrue(true);
    }
}