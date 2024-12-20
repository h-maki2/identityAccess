<?php

namespace packages\domain\service\registration\definitiveRegistration;

use Carbon\Unit;
use DateTimeImmutable;
use DomainException;
use packages\application\common\exception\TransactionException;
use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\IDefinitiveRegistrationConfirmationRepository;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\common\transactionManage\TransactionManage;
use RuntimeException;

/**
 * 認証アカウントを本登録済みに更新するサービス
 */
class DefinitiveRegistrationUpdate
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;
    private IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository;
    private TransactionManage $transactionManage;

    public function __construct(
        IAuthenticationAccountRepository $authenticationAccountRepository,
        IDefinitiveRegistrationConfirmationRepository $definitiveRegistrationConfirmationRepository,
        TransactionManage $transactionManage
    ) {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
        $this->definitiveRegistrationConfirmationRepository = $definitiveRegistrationConfirmationRepository;
        $this->transactionManage = $transactionManage;
    }

    /**
     * 認証アカウントを本登録済みに更新する
     * 更新に成功した場合はtrue、失敗した場合はfalseを返す
     */
    public function handle(OneTimeTokenValue $oneTimeTokenValue, OneTimePassword $oneTimePassword): void
    {
        $definitiveRegistrationConfirmation = $this->definitiveRegistrationConfirmationRepository->findByTokenValue($oneTimeTokenValue);

        $authAccount = $this->authenticationAccountRepository->findById($definitiveRegistrationConfirmation->userId, UnsubscribeStatus::Subscribed);
        $authAccount->updateDefinitiveRegistrationCompleted(
            $definitiveRegistrationConfirmation,
            $oneTimePassword,
            new DateTimeImmutable()
        );

        try {
            $this->transactionManage->performTransaction(function () use ($authAccount) {
                $this->authenticationAccountRepository->save($authAccount);
                $this->definitiveRegistrationConfirmationRepository->delete($authAccount->id());
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }
    }
}