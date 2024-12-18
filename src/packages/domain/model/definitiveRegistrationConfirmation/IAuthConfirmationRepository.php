<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use packages\domain\model\authenticationAccount\UserId;

interface IDefinitiveRegistrationConfirmationRepository
{
    public function findByTokenValue(OneTimeTokenValue $tokenValue): ?DefinitiveRegistrationConfirmation;

    public function findById(UserId $userId): ?DefinitiveRegistrationConfirmation;

    public function save(DefinitiveRegistrationConfirmation $authAccount): void;

    public function delete(UserId $id): void;
}
