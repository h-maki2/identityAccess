<?php

namespace packages\domain\model\authConfirmation;

interface IAuthConfirmationRepository
{
    public function findByToken(OneTimeTokenValue $tokenValue): ?AuthConfirmation;

    public function save(AuthConfirmation $authInformation): void;

    public function delete(AuthConfirmation $authInformation): void;
}
