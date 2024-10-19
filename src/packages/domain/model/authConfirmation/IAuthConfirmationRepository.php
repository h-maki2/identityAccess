<?php

namespace packages\domain\model\authConfirmation;

interface IAuthConfirmationRepository
{
    public function nextTemporaryToken(): TemporaryToken;
}
