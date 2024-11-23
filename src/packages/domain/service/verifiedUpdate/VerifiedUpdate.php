<?php

namespace packages\domain\service\verifiedUpdate;

use Carbon\Unit;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\AuthenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use RuntimeException;

/**
 * 認証情報を認証済みに更新するサービス
 */
class VerifiedUpdate
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UnitOfWork $unitOfWork;

    public function __construct(
        IAuthenticationInformationRepository $authenticationInformationRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork
    ) {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * 認証情報を認証済みに更新する
     * 更新に成功した場合はtrue、失敗した場合はfalseを返す
     */
    public function handle(AuthConfirmation $authConfirmation, OneTimePassword $password): bool
    {
        if (!$authConfirmation->oneTimePassword()->equals($password)) {
            return false;
        }

        $authInformation = $this->authenticationInformationRepository->findById($authConfirmation->userId);
        
        $authInformation->updateVerified();

        try {
            $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
                $this->authenticationInformationRepository->save($authInformation);
                $this->authConfirmationRepository->delete($authConfirmation);
            });
        } catch (\Exception $e) {
            throw new TransactionException($e->getMessage());
        }

        return true;
    }
}