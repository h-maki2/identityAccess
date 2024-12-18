<?php

namespace packages\domain\model\authConfirmation;

use packages\domain\model\authenticationAccount\UserId;

interface IAuthConfirmationRepository
{
    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?AuthConfirmation;

    public function findById(UserId $userId): ?AuthConfirmation;

    public function save(AuthConfirmation $authInformation): void;

    public function delete(UserId $id): void;
}
