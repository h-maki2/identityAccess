<?php

namespace packages\domain\service\userRegistration;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\common\unitOfWork\UnitOfWork;
use packages\domain\service\authenticationInformaion\AuthenticationInformaionService;

class UserRegistration
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;
    private IAuthConfirmationRepository $authConfirmationRepository;
    private UnitOfWork $unitOfWork;
    private AuthenticationInformaionService $authenticationInformaionService;

    public function __construct(
        IAuthenticationInformaionRepository $authenticationInformaionRepository,
        IAuthConfirmationRepository $authConfirmationRepository,
        UnitOfWork $unitOfWork
    ) {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->unitOfWork = $unitOfWork;
        $this->authenticationInformaionService = new AuthenticationInformaionService($authenticationInformaionRepository);
    }

    /**
     * ユーザー登録を行う
     */
    public function handle(UserEmail $email, UserPassword $password): AuthConfirmation
    {
        $authInformation = AuthenticationInformaion::create(
            $this->authenticationInformaionRepository->nextUserId(),
            $email,
            $password,
            $this->authenticationInformaionService
        );

        $authConfirmation = AuthConfirmation::create($authInformation->id());

        $this->unitOfWork->performTransaction(function () use ($authInformation, $authConfirmation) {
            $this->authenticationInformaionRepository->save($authInformation);
            $this->authConfirmationRepository->save($authConfirmation);
        });

        return $authConfirmation;
    }
}