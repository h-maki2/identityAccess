<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use Tests\TestCase;

class EloquentTransactionManageTest extends TestCase
{
    private EloquentTransactionManage $eloquentTransactionManage;
    private EloquentAuthenticationAccountRepository $authenticationAccountRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentTransactionManage = new EloquentTransactionManage();
        $this->authenticationAccountRepository = new EloquentAuthenticationAccountRepository();
    }

    use DatabaseTransactions;

    public function test_トランザクションをコミットできることを確認する()
    {
        // given
        // 認証情報を生成する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $authInfo = TestAuthenticationAccountFactory::create(id: $userId);

        // when
        // トランザクションをコミットする
        $this->eloquentTransactionManage->performTransaction(function () use ($authInfo) {
            $this->authenticationAccountRepository->save($authInfo);
        });

        // then
        // 認証情報が登録されていることを確認する
        $actualAuthInfo = $this->authenticationAccountRepository->findById($userId);
        $this->assertNotEmpty($actualAuthInfo);
    }

    public function test_トランザクションをロールバックできることを確認する()
    {
        // given
        // 認証情報を生成する
        $userId = $this->authenticationAccountRepository->nextUserId();
        $authInfo = TestAuthenticationAccountFactory::create(id: $userId);

        // when
        // トランザクションをロールバックする
        try {
            $this->eloquentTransactionManage->performTransaction(function () use ($authInfo) {
                $this->authenticationAccountRepository->save($authInfo);
                throw new \Exception('ロールバックのテストです。');
            });
        } catch (\Exception $e) {
        }

        // then
        // 認証情報が登録されていないことを確認する
        $actualAuthInfo = $this->authenticationAccountRepository->findById($userId);
        $this->assertEmpty($actualAuthInfo);
    }
}