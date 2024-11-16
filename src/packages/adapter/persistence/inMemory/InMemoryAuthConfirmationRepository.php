<?php

namespace packages\adapter\persistence\inMemory;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\OneTimeToken;
use Ramsey\Uuid\Uuid;

class InMemoryAuthConfirmationRepository implements IAuthConfirmationRepository
{
    public function nextOneTimeToken(): OneTimeToken
    {
        return new OneTimeToken(Uuid::uuid7());
    }
}