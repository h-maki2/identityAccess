<?php

namespace packages\domain\model\authConfirmation;

interface IAuthConfirmationRepository
{
    public function findByToken(OneTimeToken $token): AuthConfirmation;

    public function save(AuthConfirmation $authInformation): void;

    public function delete(OneTimeToken $token): void;
}
