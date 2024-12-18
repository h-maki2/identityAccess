<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentDefinitiveRegistrationConfirmationRepository;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\test\helpers\definitiveRegistrationConfirmation\definitiveRegistrationConfirmationTestDataCreator;
use packages\test\helpers\definitiveRegistrationConfirmation\TestDefinitiveRegistrationConfirmationFactory;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use Tests\TestCase;

class EloquentDefinitiveRegistrationConfirmationRepositoryTest extends TestCase
{
    private EloquentDefinitiveRegistrationConfirmationRepository $eloquentDefinitiveRegistrationConfirmationRepository;
    private EloquentAuthenticationAccountRepository $eloquentAuthenticationAccountRepository;
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;
    private DefinitiveRegistrationConfirmationTestDataCreator $definitiveRegistrationConfirmationTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentDefinitiveRegistrationConfirmationRepository = new EloquentDefinitiveRegistrationConfirmationRepository();
        $this->eloquentAuthenticationAccountRepository = new EloquentAuthenticationAccountRepository();
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($this->eloquentAuthenticationAccountRepository);
        $this->definitiveRegistrationConfirmationTestDataCreator = new DefinitiveRegistrationConfirmationTestDataCreator(
            $this->eloquentDefinitiveRegistrationConfirmationRepository,
            $this->eloquentAuthenticationAccountRepository
        );
    }

    public function test_認証確認情報を保存できる()
    {
        // given
        // あらかじめ認証アカウントを保存しておく
        $userId =  $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $userId
        );

        // 認証確認情報を作成する
        $definitiveRegistrationConfirmation = TestDefinitiveRegistrationConfirmationFactory::createDefinitiveRegistrationConfirmation(
            userId: $userId
        );

        // when
        // 認証確認情報を保存する
        $this->eloquentDefinitiveRegistrationConfirmationRepository->save($definitiveRegistrationConfirmation);

        // then
        // 認証確認情報が保存されていることを確認する
        $actualDefinitiveRegistrationConfirmation = $this->eloquentDefinitiveRegistrationConfirmationRepository->findById($userId);
        $this->assertEquals($definitiveRegistrationConfirmation->oneTimePassword(), $actualDefinitiveRegistrationConfirmation->oneTimePassword());
        $this->assertEquals($definitiveRegistrationConfirmation->oneTimeToken()->tokenValue(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->tokenValue());
        $this->assertEquals($definitiveRegistrationConfirmation->oneTimeToken()->expirationdate(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->expirationdate());
    }

    public function test_ユーザーIDから認証確認情報を取得できる()
    {
        // given
        // 認証アカウントを保存しておく
        $検索対象のユーザーID =  $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $検索対象のユーザーID
        );

        // 認証確認情報を保存しておく
        $検索対象の認証確認情報 = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $検索対象のユーザーID
        );

        // 検索対象ではない認証アカウントと認証確認情報を保存する
        $検索対象ではないユーザーid1 = $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $検索対象ではないユーザーid1
        );
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $検索対象ではないユーザーid1
        );

        $検索対象ではないユーザーid2 = $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $検索対象ではないユーザーid2
        );
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $検索対象ではないユーザーid2
        );

        // when
        $actualDefinitiveRegistrationConfirmation = $this->eloquentDefinitiveRegistrationConfirmationRepository->findById($検索対象のユーザーID);

        // then
        $this->assertEquals($検索対象の認証確認情報->oneTimePassword(), $actualDefinitiveRegistrationConfirmation->oneTimePassword());
        $this->assertEquals($検索対象の認証確認情報->oneTimeToken()->tokenValue(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->tokenValue());
        $this->assertEquals($検索対象の認証確認情報->oneTimeToken()->expirationdate(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->expirationdate());
    }

    public function test_ワンタイムトークンから認証確認情報を取得できる()
    {
        // given
        // 認証アカウントを作成しておく
        $検索対象のユーザーID =  $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $検索対象のユーザーID
        );

        // 認証確認情報を保存しておく
        $検索対象のワンタイムトークン値 = OneTimeTokenValue::create();
        $検索対象の認証確認情報 = $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $検索対象のユーザーID,
            oneTimeTokenValue: $検索対象のワンタイムトークン値
        );

        // 検索対象ではない認証アカウントと認証確認情報を保存する
        $検索対象ではないユーザーid = $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $検索対象ではないユーザーid
        );
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $検索対象ではないユーザーid,
            oneTimeTokenValue: OneTimeTokenValue::create(),
        );

        // when
        $actualDefinitiveRegistrationConfirmation = $this->eloquentDefinitiveRegistrationConfirmationRepository->findByTokenValue($検索対象のワンタイムトークン値);

        // then
        $this->assertEquals($検索対象の認証確認情報->oneTimePassword(), $actualDefinitiveRegistrationConfirmation->oneTimePassword());
        $this->assertEquals($検索対象の認証確認情報->oneTimeToken()->tokenValue(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->tokenValue());
        $this->assertEquals($検索対象の認証確認情報->oneTimeToken()->expirationdate(), $actualDefinitiveRegistrationConfirmation->oneTimeToken()->expirationdate());
    }

    public function test_認証確認情報を削除できる()
    {
        // given
        // 認証確認情報を作成して保存する
        $削除対象のユーザーID =  $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $削除対象のユーザーID
        );

        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $削除対象のユーザーID
        );

        $削除対象ではないuserId = $this->eloquentAuthenticationAccountRepository->nextUserId();
        $this->authenticationAccountTestDataCreator->create(
            id: $削除対象ではないuserId
        );
        $this->definitiveRegistrationConfirmationTestDataCreator->create(
            userId: $削除対象ではないuserId
        );

        // when
        $this->eloquentDefinitiveRegistrationConfirmationRepository->delete($削除対象のユーザーID);

        // then
        $actualDefinitiveRegistrationConfirmation = $this->eloquentDefinitiveRegistrationConfirmationRepository->findById($削除対象のユーザーID);
        $this->assertNull($actualDefinitiveRegistrationConfirmation);
    }
}