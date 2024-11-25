<?php

namespace packages\domain\model\authConfirmation;

use packages\domain\model\authenticationInformation\UserId;

interface IAuthConfirmationRepository
{
    public function findByToken(OneTimeTokenValue $tokenValue): ?AuthConfirmation;

    public function findById(UserId $userId): ?AuthConfirmation;

    public function save(AuthConfirmation $authInformation): void;

    public function delete(AuthConfirmation $authInformation): void;
}
