<?php

namespace packages\domain\model\authConfirmation;

interface IAuthConfirmationRepository
{
    public function findByToken(TemporaryToken $token): AuthConfirmation;

    public function save(AuthConfirmation $authInformation): void;

    public function delete(TemporaryToken $token): void;

    public function nextTemporaryToken(): TemporaryToken;
}
