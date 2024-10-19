<?php

namespace packages\adapter\persistence\inMemory;

use packages\domain\model\authConfirmation\IAuthConfirmationRepository;
use packages\domain\model\authConfirmation\TemporaryToken;
use Ramsey\Uuid\Uuid;

class InMemoryAuthConfirmationRepository implements IAuthConfirmationRepository
{
    public function nextTemporaryToken(): TemporaryToken
    {
        return new TemporaryToken(Uuid::uuid7());
    }
}