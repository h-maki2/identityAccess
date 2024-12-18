<?php

namespace packages\domain\service\verifiedUpdate;

use Carbon\Unit;
use DateTimeImmutable;
use DomainException;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\common\transactionManage\TransactionManage;
use RuntimeException;

/**
 * 認証情報を認証済みに更新するサービス
 */
class VerifiedUpdate
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private TransactionManage $transactionManage;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        TransactionManage $transactionManage
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->transactionManage = $transactionManage;
    }

    /**
     * 認証情報を認証済みに更新する
     * 更新に成功した場合はtrue、失敗した場合はfalseを返す
     */
    public function handle(OneTimeTokenValue $oneTimeTokenValue, OneTimePassword $oneTimePassword): void
    {
        $authConfirmation = $this->authConfirmationRepository->findByTokenValue($oneTimeTokenValue);

        if (!$authConfirmation->canUpdateVerifiedAuthInfo($oneTimePassword, new DateTimeImmutable())) {
            throw new DomainException('認証情報を認証済みに更新できませんでした。');
        } 

        $authInformation = $this->authenticationAccountRepository->findById($authConfirmation->userId);
        $authInformation->updateVerified();

        try {
            $this->transactionManage->performTransaction(function () use ($authInformation) {
                $this->authenticationAccountRepository->save($authInformation);
                $this->authConfirmationRepository->delete($authInformation->id());
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }
    }
}