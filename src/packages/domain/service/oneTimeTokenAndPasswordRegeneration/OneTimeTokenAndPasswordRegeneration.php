<?php

namespace packages\domain\service\oneTimeTokenAndPasswordRegeneration;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\email\IEmailSender;

class OneTimeTokenAndPasswordRegeneration
{
    private IAuthConfirmationRepository $authConfirmationRepository;
    private IEmailSender $emailSender;

    public function __construct(
        IAuthConfirmationRepository $authConfirmationRepository,
        IEmailSender $emailSender
    ) {
        $this->authConfirmationRepository = $authConfirmationRepository;
        $this->emailSender = $emailSender;
    }


    public function handle()
    {

    }


}