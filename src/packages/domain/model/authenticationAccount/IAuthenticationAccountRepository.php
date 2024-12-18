<?php

namespace packages\domain\model\authenticationAccount;

interface IAuthenticationAccountRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationAccount;

    public function findById(UserId $id, UnsubscribeStatus $unsubscribeStatus): ?AuthenticationAccount;

    public function save(AuthenticationAccount $authenticationAccount): void;

    public function delete(AuthenticationAccount $authenticationAccount): void;

    public function nextUserId(): UserId;
}