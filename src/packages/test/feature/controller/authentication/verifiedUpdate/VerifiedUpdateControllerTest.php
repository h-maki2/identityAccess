<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
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

    public function test_正しいワンタイムトークンとワンタイムパスワードを入力すると本登録が完了する()
    {
        // given
        // 本登録が済んでいない認証アカウントを作成して保存する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $authConfirmation = $this->authConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 確認済み更新を行う
        $response = $this->post('/verifiedUpdate', [
            'oneTimeToken' => $authConfirmation->oneTimeToken()->tokenValue()->value,
            'oneTimePassword' => $authConfirmation->oneTimePassword()->value
        ]);

        // then
        $response->assertStatus(200);
        // 本登録完了画面に遷移することを確認する
        $content = htmlspecialchars_decode($response->getContent());
        $this->assertStringContainsString('<title>本登録完了</title>', $content);
    }

    public function test_ワンタイムパスワードとワンタイムトークンが正しくない場合に、本登録の更新に失敗する()
    {
        // given
        // 本登録が済んでいない認証アカウントを作成して保存する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            verificationStatus: VerificationStatus::Unverified
        );

        // 認証確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct(str_repeat('a', 26));
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $this->authConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePassword
        );

        // when
        // 確認済み更新を行う
        $invalidOneTimeTokenValue = str_repeat('b', 26);
        $invalidOneTimePassword = '654321';
        $response = $this->post('/verifiedUpdate', [
            'oneTimeToken' => $invalidOneTimeTokenValue,
            'oneTimePassword' => $invalidOneTimePassword
        ]);

        // then
        // 本登録画面にリダイレクトすることを確認する
        $response->assertStatus(302);
    }
}