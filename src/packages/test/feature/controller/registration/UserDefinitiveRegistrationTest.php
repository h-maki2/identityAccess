<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use packages\test\helpers\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class UserDefinitiveRegistrationTest extends TestCase
{
    private EloquentDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->definitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->authenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator($this->definitiveRegistrationConfirmationRepository, $this->authenticationAccountRepository);
    }

    public function test_正しいワンタイムトークンとワンタイムパスワードを入力すると本登録が完了する()
    {
        // given
        // 本登録が済んでいない認証アカウントを作成して保存する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId,
            definitiveRegistrationCompletedStatus: definitiveRegistrationCompletedStatus::Incomplete
        );

        // 本登録確認を作成して保存する
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationTestDataCreator->create(userId: $userId);

        // when
        // 本登録済み更新を行う
        $response = $this->post('/definitiveRegister', [
            'oneTimeToken' => $definitiveRegistrationConfirmation->oneTimeToken()->tokenValue()->value,
            'oneTimePassword' => $definitiveRegistrationConfirmation->oneTimePassword()->value
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
            definitiveRegistrationCompletedStatus: definitiveRegistrationCompletedStatus::Incomplete
        );

        // 本登録確認を作成して保存する
        $oneTimeTokenValue = OneTimeTokenValue::reconstruct(str_repeat('a', 26));
        $oneTimePassword = OneTimePassword::reconstruct('123456');
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $userId,
            oneTimeTokenValue: $oneTimeTokenValue,
            oneTimePassword: $oneTimePassword
        );

        // when
        // 本登録済み更新を行う
        $invalidOneTimeTokenValue = str_repeat('b', 26);
        $invalidOneTimePassword = '654321';
        $response = $this->post('/definitiveRegister', [
            'oneTimeToken' => $invalidOneTimeTokenValue,
            'oneTimePassword' => $invalidOneTimePassword
        ]);

        // then
        // 本登録画面にリダイレクトすることを確認する
        $response->assertStatus(302);
    }
}