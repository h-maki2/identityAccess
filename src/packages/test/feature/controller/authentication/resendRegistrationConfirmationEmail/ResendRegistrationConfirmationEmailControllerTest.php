<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
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
        //
    }
}