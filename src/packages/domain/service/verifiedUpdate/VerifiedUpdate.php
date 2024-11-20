<?php

namespace packages\domain\service\verifiedUpdate;

use Carbon\Unit;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use RuntimeException;

/**
 * 認証情報を認証済みに更新するサービス
 */
class VerifiedUpdate
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UnitOfWork $unitOfWork;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork
    ) {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
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

        $authInformation = $this->authenticationInformaionRepository->findById($authConfirmation->userId);
        if ($authInformation === null) {
            throw new RuntimeException('認証情報が見つかりませんでした。');
        }

        $authInformation->updateVerified();

        $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
            $this->authenticationInformaionRepository->save($authInformation);
            $this->authConfirmationRepository->delete($authConfirmation);
        });
    }
}