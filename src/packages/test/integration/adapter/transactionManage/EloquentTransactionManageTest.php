<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\adapter\persistence\eloquent\EloquentAuthenticationInformationRepository;
use packages\adapter\transactionManage\EloquentTransactionManage;
use packages\test\helpers\authenticationInformation\TestAuthenticationInformationFactory;
use Tests\TestCase;

class EloquentTransactionManageTest extends TestCase
{
    private EloquentTransactionManage $eloquentTransactionManage;
    private EloquentAuthenticationInformationRepository $authenticationInformationRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->eloquentTransactionManage = new EloquentTransactionManage();
        $this->authenticationInformationRepository = new EloquentAuthenticationInformationRepository();
    }

    use DatabaseTransactions;

    public function test_トランザクションをコミットできることを確認する()
    {
        // given
        // 認証情報を生成する
        $userId = $this->authenticationInformationRepository->nextUserId();
        $authInfo = TestAuthenticationInformationFactory::create(id: $userId);

        // when
        // トランザクションをコミットする
        $this->eloquentTransactionManage->performTransaction(function () use ($authInfo) {
            $this->authenticationInformationRepository->save($authInfo);
        });

        // then
        // 認証情報が登録されていることを確認する
        $actualAuthInfo = $this->authenticationInformationRepository->findById($userId);
        $this->assertNotEmpty($actualAuthInfo);
    }

    public function test_トランザクションをロールバックできることを確認する()
    {
        // given
        // 認証情報を生成する
        $userId = $this->authenticationInformationRepository->nextUserId();
        $authInfo = TestAuthenticationInformationFactory::create(id: $userId);

        // when
        // トランザクションをロールバックする
        try {
            $this->eloquentTransactionManage->performTransaction(function () use ($authInfo) {
                $this->authenticationInformationRepository->save($authInfo);
                throw new \Exception('ロールバックのテストです。');
            });
        } catch (\Exception $e) {
        }

        // then
        // 認証情報が登録されていないことを確認する
        $actualAuthInfo = $this->authenticationInformationRepository->findById($userId);
        $this->assertEmpty($actualAuthInfo);
    }
}