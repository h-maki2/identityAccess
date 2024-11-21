<?php

use packages\adapter\persistence\inMemory\InMemoryAuthConfirmationRepository;
use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformaionRepository;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationApplicationService;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\VerificationStatus;
use packages\test\helpers\authConfirmation\AuthConfirmationTestDataCreator;
use packages\test\helpers\authenticationInformaion\AuthenticationInformaionTestDataCreator;
use PHPUnit\Framework\TestCase;

class OneTimeTokenAndPasswordRegenerationApplicationServiceTest extends TestCase
{
    private InMemoryAuthConfirmationRepository $authConfirmationRepository;
    private InMemoryAuthenticationInformaionRepository $authenticationInformaionRepository;
    private OneTimeTokenAndPasswordRegenerationApplicationService $oneTimeTokenAndPasswordRegenerationApplicationService;
    private AuthConfirmationTestDataCreator $authConfirmationTestDataCreator;
    private AuthenticationInformaionTestDataCreator $authenticationInformaionTestDataCreator;

    public function setUp(): void
    {
        $this->authConfirmationRepository = new InMemoryAuthConfirmationRepository();
        $this->authenticationInformaionRepository = new InMemoryAuthenticationInformaionRepository();
        $this->oneTimeTokenAndPasswordRegenerationApplicationService = new OneTimeTokenAndPasswordRegenerationApplicationService(
            $this->authConfirmationRepository,
            $this->authenticationInformaionRepository
        );
        $this->authConfirmationTestDataCreator = new AuthConfirmationTestDataCreator($this->authConfirmationRepository, $this->authenticationInformaionRepository);
        $this->authenticationInformaionTestDataCreator = new AuthenticationInformaionTestDataCreator($this->authenticationInformaionRepository);
    }

    public function test_認証情報は認証済みではない場合、ワンタイムトークンとワンタイムパスワードの再生成ができる()
    {
        // given
        // 認証情報を作成して保存する
        $userEmail = new UserEmail('test@example.com');
        $authenticationInformaion = $this->authenticationInformaionTestDataCreator->create(
            email: $userEmail,
            verificationStatus: VerificationStatus::Unverified // 認証済みではない
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::create();
        $oneTimePasword = OneTimePassword::create();
        $this->authConfirmationTestDataCreator->create(
            userId: $authenticationInformaion->id(),
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePasword
        );

        // when
        $result = $this->oneTimeTokenAndPasswordRegenerationApplicationService->regenerateOneTimeTokenAndPassword($userEmail->value);

        // then
        // バリデーションエラーが発生していないことを確認
        $this->assertFalse($result->validationError);
        $this->assertEmpty($result->validationErrorMessage);

        // ワンタイムトークンが再生成されていることを確認
        $this->assertNotEquals($oneTimeTokenValue->value, $result->oneTimeTokenValue);

        // ワンタイムパスワードが再生成されていることを確認
        $actualAuthConfirmation = $this->authConfirmationRepository->findById($authenticationInformaion->id());
        $this->assertNotEquals($oneTimePasword->value, $actualAuthConfirmation->oneTimePassword()->value);
    }

}